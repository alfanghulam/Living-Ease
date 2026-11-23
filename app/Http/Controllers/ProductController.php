<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

public function show(Product $product)
{
    abort_if(!$product->is_active, 404);

    $related = Product::withRating()
        ->where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->where('is_active', true)
        ->limit(4)->get();

    $product->loadMissing(['category', 'brand'])->loadCount('reviews')->loadAvg('reviews', 'rating');

    $reviews = $product->reviews()->with('user:id,name')->latest()->take(20)->get();
    $distribution = $product->reviews()
        ->selectRaw('rating, count(*) as total')
        ->groupBy('rating')
        ->pluck('total', 'rating');

    $myReview = null;
    $canReview = false;

    if (auth()->check()) {
        $myReview = $product->reviews()->where('user_id', auth()->id())->first();
        $canReview = ReviewController::eligibleOrder(auth()->id(), $product) !== null;
    }

    return view('products.show', compact('product', 'related', 'reviews', 'distribution', 'myReview', 'canReview'));
}
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])->withRating()->where('is_active', true);

        if ($request->filled('cat_id')) {
            $query->where('category_id', $request->cat_id);
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%$q%")
                    ->orWhere('keywords', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }

        $products = $query->latest()->get();

        return view('products.index', compact('products'));
    }
}