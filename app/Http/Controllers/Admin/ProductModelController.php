<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateProductModel;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductModelController extends Controller
{
    /** Antre pembuatan model 3D dari foto produk. */
    public function generate(Product $product)
    {
        if (! config('model3d.enabled')) {
            return back()->with('error', 'Fitur model 3D dimatikan (MODEL3D_ENABLED=false).');
        }

        if (in_array($product->model_status, ['queued', 'processing'], true)) {
            return back()->with('error', 'Model 3D untuk produk ini sedang diproses.');
        }

        $product->forceFill(['model_status' => 'queued', 'model_error' => null])->save();
        GenerateProductModel::dispatch($product->id);

        return back()->with('success', 'Model 3D mulai dibuat. Halaman ini akan memperbarui statusnya otomatis.');
    }

    /** Dipoll oleh halaman edit untuk menampilkan progres. */
    public function status(Product $product)
    {
        return response()->json([
            'status' => $product->model_status,
            'error' => $product->model_error,
            'url' => $product->model_url,
        ]);
    }

    /** Cadangan: unggah file .glb buatan sendiri (mis. hasil scan atau Blender). */
    public function upload(Request $request, Product $product)
    {
        $request->validate([
            'model' => ['required', 'file', 'max:20480', function ($attribute, $value, $fail) {
                $isGlb = strtolower($value->getClientOriginalExtension()) === 'glb'
                    && str_starts_with((string) file_get_contents($value->getRealPath(), false, null, 0, 4), 'glTF');

                if (! $isGlb) {
                    $fail('File harus berformat .glb yang valid.');
                }
            }],
        ]);

        $path = $request->file('model')->storeAs('models', $product->id.'-'.time().'.glb', 'public');

        if ($product->model_path) {
            Storage::disk('public')->delete($product->model_path);
        }

        $product->forceFill(['model_path' => $path, 'model_status' => 'ready', 'model_error' => null])->save();

        return back()->with('success', 'Model 3D berhasil diunggah.');
    }

    public function destroy(Product $product)
    {
        if ($product->model_path) {
            Storage::disk('public')->delete($product->model_path);
        }

        $product->forceFill(['model_path' => null, 'model_status' => null, 'model_error' => null])->save();

        return back()->with('success', 'Model 3D dihapus. Halaman produk kembali memakai foto.');
    }
}
