<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateProductModel;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand'])->latest()->get();
        $missingShipping = $products->reject(fn ($p) => $p->hasShippingData())->count();

    return view('admin.products.index', compact('products', 'missingShipping'));
    }

    public function create()
    {
        $categories = Category::orderBy('title')->get();
        $brands = Brand::orderBy('title')->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'required|exists:brands,id',
            'title'       => 'required|string|max:255',
            'sku'         => ['nullable', 'string', 'max:50', Rule::unique('products', 'sku')],
            'price'       => 'required|integer|min:0',
            'weight_grams' => 'nullable|integer|min:1|max:500000',
            'length_cm'   => 'nullable|integer|min:1|max:1000',
            'width_cm'    => 'nullable|integer|min:1|max:1000',
            'height_cm'   => 'nullable|integer|min:1|max:1000',
            'discount_price' => ['nullable', 'integer', 'min:0', 'lt:price'],
            'stock'       => 'required|integer|min:0',
            'description' => 'required|string',
            'keywords'    => 'nullable|string',
            'image'       => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $path = $request->file('image')->store('products', 'public');

        $product = Product::create([
            'category_id' => $data['category_id'],
            'brand_id'    => $data['brand_id'],
            'title'       => $data['title'],
            'sku'         => $data['sku'] ?? null,
            'price'       => $data['price'],
            'discount_price' => $data['discount_price'] ?? null,
            'stock'       => $data['stock'],
            'weight_grams' => $data['weight_grams'] ?? null,
            'length_cm'   => $data['length_cm'] ?? null,
            'width_cm'    => $data['width_cm'] ?? null,
            'height_cm'   => $data['height_cm'] ?? null,
            'description' => $data['description'],
            'keywords'    => $data['keywords'] ?? '',
            'image'       => $path,
        ]);

        $queued = $this->maybeQueueModel($request, $product);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.'.($queued ? ' Model 3D sedang dibuat.' : ''));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('title')->get();
        $brands = Brand::orderBy('title')->get();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'required|exists:brands,id',
            'title'       => 'required|string|max:255',
            'sku'         => ['nullable', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product->id)],
            'price'       => 'required|integer|min:0',
            'weight_grams' => 'nullable|integer|min:1|max:500000',
            'length_cm'   => 'nullable|integer|min:1|max:1000',
            'width_cm'    => 'nullable|integer|min:1|max:1000',
            'height_cm'   => 'nullable|integer|min:1|max:1000',
            'discount_price' => ['nullable', 'integer', 'min:0', 'lt:price'],
            'stock'       => 'required|integer|min:0',
            'description' => 'required|string',
            'keywords'    => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $product->category_id = $data['category_id'];
        $product->brand_id    = $data['brand_id'];
        $product->title       = $data['title'];
        $product->sku         = $data['sku'] ?? null;
        $product->price       = $data['price'];
        $product->discount_price = $data['discount_price'] ?? null;
        $product->stock       = $data['stock'];
        $product->weight_grams = $data['weight_grams'] ?? null;
        $product->length_cm   = $data['length_cm'] ?? null;
        $product->width_cm    = $data['width_cm'] ?? null;
        $product->height_cm   = $data['height_cm'] ?? null;
        $product->description = $data['description'];
        $product->keywords    = $data['keywords'] ?? '';

        if ($request->hasFile('image')) {
            if (str_contains($product->image, '/')) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->save();

        $queued = $this->maybeQueueModel($request, $product);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.'.($queued ? ' Model 3D sedang dibuat.' : ''));
    }

    /** Centang "Buat model 3D otomatis": antre pembuatan model dari foto produk. */
    protected function maybeQueueModel(Request $request, Product $product): bool
    {
        if (! $request->boolean('generate_3d') || ! config('model3d.enabled')) {
            return false;
        }

        $product->forceFill(['model_status' => 'queued', 'model_error' => null])->save();
        GenerateProductModel::dispatch($product->id);

        return true;
    }

    public function toggleActive(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.products.index')->with('success', "Produk \"{$product->title}\" berhasil {$status}.");
    }

    public function destroy(Product $product)
    {
        try {
            if (str_contains($product->image, '/')) {
                Storage::disk('public')->delete($product->image);
            }
            if ($product->model_path) {
                Storage::disk('public')->delete($product->model_path);
            }
            $product->delete();

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.products.index')->with('error', 'Produk "'.$product->title.'" tidak bisa dihapus karena sudah pernah ada transaksi/pesanan. Silakan nonaktifkan saja produk ini.');
        }
    }
}
