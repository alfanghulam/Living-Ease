<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    public function isConfigured(): bool
    {
        return filled(config('chatbot.key'));
    }

    /**
     * Riwayat percakapan untuk satu sesi (terlama → terbaru).
     */
    public function messages(string $sessionId, int $limit = 50): array
    {
        return ChatMessage::where('session_id', $sessionId)
            ->latest('id')
            ->take($limit)
            ->get()
            ->reverse()
            ->map(fn (ChatMessage $m) => [
                'id' => $m->id,
                'role' => $m->role,
                'content' => $m->content,
                'at' => $m->created_at?->toIso8601String(),
            ])
            ->values()
            ->all();
    }

    /**
     * Simpan pesan pembeli lalu minta jawaban ke model.
     *
     * @return array{reply: string, configured: bool}
     */
    public function reply(string $sessionId, string $message, ?int $userId = null): array
    {
        ChatMessage::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'role' => 'user',
            'content' => $message,
        ]);

        if (! $this->isConfigured()) {
            $fallback = $this->fallbackMessage();
            $this->storeAssistant($sessionId, $userId, $fallback, ['configured' => false]);

            return [
                'reply' => $fallback,
                'configured' => false,
            ];
        }

        try {
            $response = Http::withToken(config('chatbot.key'))
                ->acceptJson()
                ->timeout(config('chatbot.timeout'))
                ->post(rtrim(config('chatbot.base_url'), '/').'/chat/completions', [
                    'model' => config('chatbot.model'),
                    'messages' => $this->buildMessages($sessionId),
                    'temperature' => config('chatbot.temperature'),
                    'max_tokens' => config('chatbot.max_tokens'),
                ]);
        } catch (\Throwable $e) {
            Log::warning('Chatbot request failed', ['error' => $e->getMessage()]);
            $fallback = $this->fallbackMessage();
            $this->storeAssistant($sessionId, $userId, $fallback, ['error' => true]);

            return ['reply' => $fallback, 'configured' => true];
        }

        $text = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

        if (! $response->successful() || $text === '') {
            Log::warning('Chatbot bad response', [
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 500),
            ]);

            $fallback = $this->fallbackMessage();
            $this->storeAssistant($sessionId, $userId, $fallback, ['error' => true]);

            return ['reply' => $fallback, 'configured' => true];
        }

        $this->storeAssistant($sessionId, $userId, $text, ['model' => config('chatbot.model')]);

        return ['reply' => $text, 'configured' => true];
    }

    private function storeAssistant(string $sessionId, ?int $userId, string $content, array $meta = []): void
    {
        ChatMessage::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'role' => 'assistant',
            'content' => $content,
            'meta' => $meta,
        ]);
    }

    private function buildMessages(string $sessionId): array
    {
        $limit = max(1, (int) config('chatbot.history_limit'));

        $history = ChatMessage::where('session_id', $sessionId)
            ->latest('id')
            ->take($limit)
            ->get()
            ->reverse()
            ->map(fn (ChatMessage $m) => ['role' => $m->role, 'content' => $m->content])
            ->values()
            ->all();

        return array_merge(
            [['role' => 'system', 'content' => $this->systemPrompt()]],
            $history
        );
    }

    private function systemPrompt(): string
    {
        $store = Setting::get('store_name') ?: config('chatbot.store_name');
        $wa = Setting::get('chatbot_whatsapp') ?: config('chatbot.whatsapp');

        $promo = Setting::get('promo_active') === '1'
            ? '- Promo aktif: kode '.Setting::get('promo_code').' untuk potongan '.Setting::get('promo_percent').'% (sekali pakai per akun).'
            : '- Saat ini tidak ada kode promo aktif.';

            $expire = (int) config('shop.order_expire_minutes');

        return <<<PROMPT
        Kamu "Bot", asisten belanja di {$store} (toko online di Yogyakarta).

        Gaya bicara: santai, ramah, seperti kakak yang paham produk. Bahasa Indonesia. Maksimal 4 kalimat pendek (±60 kata). Boleh pakai 1 emoji, jangan berlebihan.

        Yang kamu tahu tentang toko:
        - Pembayaran online lewat Midtrans; metodenya dipilih pembeli di halaman checkout.
        - Pesanan yang belum dibayar dalam {$expire} menit dibatalkan otomatis.
        {$promo}
        - Soal ongkir, garansi, dan jam pengiriman, arahkan ke admin WhatsApp {$wa}..

        Aturan penting:
        - Hanya sebutkan produk dan harga yang ada di daftar katalog di bawah. Jangan mengarang produk, stok pasti, atau kebijakan baru.
        - Kalau ditanya stok pasti, ongkir ke kota tertentu, status pesanan, atau komplain, arahkan ke admin/WhatsApp {$wa}.
        - Jangan pernah meminta data sensitif (password, nomor kartu, OTP).

        Katalog produk (nama | harga | kategori):
        {$this->catalogContext()}
        PROMPT;
    }

    private function catalogContext(): string
    {
        return Cache::remember('chatbot.catalog', config('chatbot.catalog_ttl'), function () {
            return Product::with('category')
                ->where('is_active', true)
                ->orderBy('title')
                ->take(30)
                ->get()
                ->map(fn (Product $p) => sprintf(
                    '- %s | Rp %s | %s',
                    $p->title,
                    number_format($p->final_price, 0, ',', '.'),
                    $p->category?->title ?? 'lainnya'
                ))
                ->implode("\n");
        });
    }

    private function fallbackMessage(): string
    {
        $wa = Setting::get('chatbot_whatsapp') ?: config('chatbot.whatsapp');

        return "Asisten AI-nya lagi istirahat sebentar 🙏 Untuk pertanyaan cepat, chat admin kami di WhatsApp {$wa} ya.";
    }
}
