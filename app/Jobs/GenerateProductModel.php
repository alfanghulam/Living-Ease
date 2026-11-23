<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\Model3D\ProductModelGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class GenerateProductModel implements ShouldQueue
{
    use Queueable;

    public int $timeout = 900;
    public int $tries = 1;

    public function __construct(public int $productId) {}

    public function handle(ProductModelGenerator $generator): void
    {
        $product = Product::find($this->productId);

        if (! $product) {
            return;
        }

        $product->forceFill(['model_status' => 'processing', 'model_error' => null])->save();

        $oldPath = $product->model_path;
        $path = $generator->generate($product);

        $product->forceFill([
            'model_path' => $path,
            'model_status' => 'ready',
            'model_error' => null,
        ])->save();

        if ($oldPath && $oldPath !== $path) {
            Storage::disk('public')->delete($oldPath);
        }
    }

    public function failed(Throwable $e): void
    {
        Log::warning('Model 3D gagal dibuat', ['product' => $this->productId, 'error' => $e->getMessage()]);

        Product::whereKey($this->productId)->update([
            'model_status' => 'failed',
            'model_error' => Str::limit($e->getMessage(), 480, ''),
        ]);
    }
}
