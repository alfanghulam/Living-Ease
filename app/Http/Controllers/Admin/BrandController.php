<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $items = Brand::withCount('products')->orderBy('title')->get();

        $editing = $request->filled('edit')
            ? Brand::find($request->query('edit'))
            : null;

        return view('admin.catalog.taxonomy', [
            'title' => 'Brand',
            'singular' => 'brand',
            'routeBase' => 'admin.brands',
            'items' => $items,
            'editing' => $editing,
            'productsLabel' => 'Produk',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:100', Rule::unique('brands', 'title')],
        ]);

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand ditambahkan.');
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:100', Rule::unique('brands', 'title')->ignore($brand->id)],
        ]);

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand diperbarui.');
    }

    public function destroy(Brand $brand)
    {
        $count = $brand->products()->count();

        if ($count > 0) {
            return back()->with('error', "Brand “{$brand->title}” masih dipakai {$count} produk. Pindahkan produknya dulu.");
        }

        $brand->delete();

        return back()->with('success', 'Brand dihapus.');
    }
}
