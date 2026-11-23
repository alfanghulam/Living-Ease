<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $items = Category::withCount('products')->orderBy('title')->get();

        $editing = $request->filled('edit')
            ? Category::find($request->query('edit'))
            : null;

        return view('admin.catalog.taxonomy', [
            'title' => 'Kategori',
            'singular' => 'kategori',
            'routeBase' => 'admin.categories',
            'items' => $items,
            'editing' => $editing,
            'productsLabel' => 'Produk',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:100', Rule::unique('categories', 'title')],
        ]);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:100', Rule::unique('categories', 'title')->ignore($category->id)],
        ]);

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        $count = $category->products()->count();

        if ($count > 0) {
            return back()->with('error', "Kategori “{$category->title}” masih dipakai {$count} produk. Pindahkan produknya dulu.");
        }

        $category->delete();

        return back()->with('success', 'Kategori dihapus.');
    }
}
