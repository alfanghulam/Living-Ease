<?php

namespace App\Services\Model3D;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/** Foto produk (satu sisi) -> model 3D .glb lewat Space Hugging Face. */
class ProductModelGenerator
{
    public function __construct(protected ?GradioClient $client = null) {}

    /** @return string path relatif di disk "public" */
    public function generate(Product $product): string
    {
        $client = $this->client ?? GradioClient::fromConfig();
        $source = $this->sourceImagePath($product);

        $image = $client->upload($source);
        $previous = [];

        foreach ((array) config('model3d.steps') as $step) {
            $data = array_map(function ($value) use ($image, $previous) {
                if ($value === '@image') {
                    return $image;
                }
                if ($value === '@previous') {
                    return GradioClient::firstFile($previous) ?? ($previous[0] ?? $image);
                }

                return $value;
            }, $step['data'] ?? []);

            $previous = $client->call($step['api'], $data);
        }

        $file = GradioClient::firstFile($previous, 'glb');

        if (! $file) {
            throw new RuntimeException('Space tidak mengembalikan file .glb. Cek konfigurasi langkah di config/model3d.php.');
        }

        $binary = $client->download($file);

        if (! str_starts_with($binary, 'glTF')) {
            throw new RuntimeException('File hasil dari Space bukan model GLB yang valid.');
        }

        $path = 'models/'.$product->id.'-'.time().'.glb';
        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    protected function sourceImagePath(Product $product): string
    {
        $path = str_contains((string) $product->image, '/')
            ? Storage::disk('public')->path($product->image)
            : public_path('img/products/'.$product->image);

        if (! is_file($path)) {
            throw new RuntimeException('File foto produk tidak ditemukan di server.');
        }

        return $path;
    }
}
