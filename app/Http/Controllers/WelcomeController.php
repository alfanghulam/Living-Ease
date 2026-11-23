<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class WelcomeController extends Controller
{
    public function index()
    {
        $featured = Product::with(['category', 'brand'])
            ->withRating()
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::orderBy('id')->take(6)->get();

        $categoryCounts = Product::where('is_active', true)
            ->selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $stats = [
            'products' => Product::where('is_active', true)->count(),
            'categories' => Category::count(),
            'brands' => Brand::count(),
        ];

        return view('welcome', compact('featured', 'categories', 'categoryCounts', 'stats'));
    }
}
