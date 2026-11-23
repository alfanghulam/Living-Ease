<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /** Hanya pembeli dengan pesanan SELESAI yang berisi produk ini yang boleh memberi ulasan. */
    public static function eligibleOrder(int $userId, Product $product): ?Order
    {
        return Order::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->latest()
            ->first();
    }

    public function store(Request $request, Product $product)
    {
        abort_if(! $product->is_active, 404);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'rating.required' => 'Pilih jumlah bintang dulu.',
            'rating.between' => 'Rating harus antara 1 sampai 5.',
        ]);

        $order = self::eligibleOrder(Auth::id(), $product);

        if (! $order) {
            return $this->back($product)->with('error', 'Ulasan bisa ditulis setelah pesanan produk ini berstatus selesai.');
        }

        Review::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $product->id],
            [
                'order_id' => $order->id,
                'rating' => $data['rating'],
                'comment' => filled($data['comment'] ?? null) ? trim($data['comment']) : null,
            ]
        );

        return $this->back($product)->with('success', 'Terima kasih, ulasanmu sudah tersimpan.');
    }

    public function destroy(Review $review)
    {
        abort_if($review->user_id !== Auth::id(), 403);

        $product = $review->product;
        $review->delete();

        return $this->back($product)->with('success', 'Ulasanmu sudah dihapus.');
    }

    protected function back(Product $product)
    {
        return redirect()->to(route('products.show', $product).'#ulasan');
    }
}
