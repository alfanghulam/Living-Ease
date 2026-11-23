<?php

namespace App\Services\Model3D;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Klien HTTP minimal untuk Space Gradio (protokol /upload dan /call/{api}).
 */
class GradioClient
{
    protected ?string $resolvedPrefix = null;

    public function __construct(
        protected string $space,
        protected ?string $token = null,
        protected ?string $prefix = null,
        protected int $timeout = 600,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            (string) config('model3d.space'),
            config('model3d.token'),
            config('model3d.api_prefix'),
            (int) config('model3d.timeout', 600),
        );
    }

    public function baseUrl(): string
    {
        if (preg_match('#^https?://#i', $this->space)) {
            return rtrim($this->space, '/');
        }

        // "pemilik/Nama_Space.v2" -> "pemilik-nama-space-v2.hf.space"
        $host = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $this->space));

        return 'https://'.trim($host, '-').'.hf.space';
    }

    protected function http(): PendingRequest
    {
        $request = Http::timeout($this->timeout)->connectTimeout(20);

        return $this->token ? $request->withToken($this->token) : $request;
    }

    /** Awalan API: "/gradio_api" (Gradio 5) atau "" (Gradio 4). */
    public function prefix(): string
    {
        if ($this->prefix !== null && $this->prefix !== '') {
            return '/'.trim($this->prefix, '/');
        }

        if ($this->resolvedPrefix !== null) {
            return $this->resolvedPrefix;
        }

        $config = $this->http()->acceptJson()->get($this->baseUrl().'/config');

        if ($config->failed()) {
            throw new RuntimeException("Space tidak bisa dihubungi ({$this->baseUrl()}, HTTP {$config->status()}). Mungkin sedang tidur, penuh, atau namanya salah.");
        }

        $found = (string) $config->json('api_prefix', '');

        return $this->resolvedPrefix = $found === '' ? '' : '/'.trim($found, '/');
    }

    /** Daftar api_name yang tersedia di Space (untuk perintah model3d:check). */
    public function endpoints(): array
    {
        $config = $this->http()->acceptJson()->get($this->baseUrl().'/config');

        if ($config->failed()) {
            throw new RuntimeException("Space tidak bisa dihubungi (HTTP {$config->status()}).");
        }

        return collect($config->json('dependencies', []))
            ->filter(fn ($d) => ! empty($d['api_name']))
            ->map(fn ($d) => '/'.ltrim($d['api_name'], '/'))
            ->values()
            ->all();
    }

    /** Unggah file ke Space, kembalikan FileData siap pakai sebagai input. */
    public function upload(string $path, ?string $name = null): array
    {
        $name ??= basename($path);

        $response = $this->http()
            ->attach('files', file_get_contents($path), $name)
            ->post($this->baseUrl().$this->prefix().'/upload');

        if ($response->failed() || ! is_array($response->json()) || ! isset($response->json()[0])) {
            throw new RuntimeException("Gagal mengunggah foto ke Space (HTTP {$response->status()}).");
        }

        return [
            'path' => $response->json()[0],
            'orig_name' => $name,
            'meta' => ['_type' => 'gradio.FileData'],
        ];
    }

    /** Panggil endpoint dan tunggu hasil. @return array keluaran endpoint */
    public function call(string $apiName, array $data): array
    {
        $api = ltrim($apiName, '/');
        $url = $this->baseUrl().$this->prefix().'/call/'.$api;

        $start = $this->http()->asJson()->acceptJson()->post($url, ['data' => $data]);

        if ($start->failed() || blank($start->json('event_id'))) {
            throw new RuntimeException("Space menolak panggilan /{$api} (HTTP {$start->status()}).");
        }

        $stream = $this->http()->get($url.'/'.$start->json('event_id'));

        return self::resultFromStream($stream->body(), $api);
    }

    /** Unduh isi file keluaran (FileData). */
    public function download(array $file): string
    {
        $url = $file['url'] ?? null;

        if (! $url && ! empty($file['path'])) {
            $url = $this->baseUrl().$this->prefix().'/file='.$file['path'];
        }

        if (! $url) {
            throw new RuntimeException('Keluaran Space tidak punya alamat file.');
        }

        $response = $this->http()->get($url);

        if ($response->failed()) {
            throw new RuntimeException("Gagal mengunduh hasil dari Space (HTTP {$response->status()}).");
        }

        return $response->body();
    }

    /** Ambil hasil dari aliran SSE Gradio. */
    public static function resultFromStream(string $body, string $api = ''): array
    {
        $events = self::parseSse($body);

        foreach (array_reverse($events) as $event) {
            if ($event['event'] === 'complete') {
                $decoded = json_decode($event['data'], true);

                return is_array($decoded) ? $decoded : [];
            }

            if ($event['event'] === 'error') {
                $message = json_decode($event['data'], true);
                $message = is_string($message) ? $message : 'Space mengembalikan error (kemungkinan kuota habis atau sedang penuh).';

                throw new RuntimeException("Space error pada /{$api}: ".mb_substr($message, 0, 300));
            }
        }

        throw new RuntimeException("Respons Space untuk /{$api} tidak lengkap.");
    }

    /** @return list<array{event: string, data: string}> */
    public static function parseSse(string $body): array
    {
        $events = [];
        $current = ['event' => 'message', 'data' => ''];

        foreach (preg_split('/\r?\n/', $body) as $line) {
            if ($line === '') {
                if ($current['data'] !== '' || $current['event'] !== 'message') {
                    $events[] = $current;
                }
                $current = ['event' => 'message', 'data' => ''];
            } elseif (str_starts_with($line, 'event:')) {
                $current['event'] = trim(substr($line, 6));
            } elseif (str_starts_with($line, 'data:')) {
                $current['data'] .= ltrim(substr($line, 5));
            }
        }

        if ($current['data'] !== '' || $current['event'] !== 'message') {
            $events[] = $current;
        }

        return $events;
    }

    /** FileData pertama di keluaran (opsional dibatasi ekstensi). */
    public static function firstFile(array $outputs, ?string $extension = null): ?array
    {
        foreach ($outputs as $item) {
            if (! is_array($item)) {
                continue;
            }

            $name = strtolower((string) ($item['orig_name'] ?? $item['path'] ?? $item['url'] ?? ''));
            $isFile = isset($item['path']) || isset($item['url']);

            if ($isFile && (! $extension || str_ends_with($name, '.'.strtolower($extension)))) {
                return $item;
            }
        }

        return null;
    }
}
