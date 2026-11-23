<?php

namespace App\Console\Commands;

use App\Services\Model3D\GradioClient;
use Illuminate\Console\Command;
use Throwable;

class Model3dCheck extends Command
{
    protected $signature = 'model3d:check';

    protected $description = 'Periksa Space Hugging Face untuk pembuat model 3D dan endpoint yang dikonfigurasi';

    public function handle(): int
    {
        $client = GradioClient::fromConfig();

        $this->line('Space : '.config('model3d.space'));
        $this->line('URL   : '.$client->baseUrl());
        $this->line('Token : '.(config('model3d.token') ? 'terisi' : 'kosong (kuota gratis lebih kecil)'));

        try {
            $endpoints = $client->endpoints();
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Endpoint tersedia: '.implode(', ', $endpoints));

        $missing = collect(config('model3d.steps'))
            ->pluck('api')
            ->map(fn ($api) => '/'.ltrim($api, '/'))
            ->reject(fn ($api) => in_array($api, $endpoints, true));

        if ($missing->isNotEmpty()) {
            $this->error('Endpoint di config/model3d.php tidak ada di Space: '.$missing->implode(', '));

            return self::FAILURE;
        }

        $this->info('Semua langkah di config/model3d.php ditemukan di Space.');

        return self::SUCCESS;
    }
}
