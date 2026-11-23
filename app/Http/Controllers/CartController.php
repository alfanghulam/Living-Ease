<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        // Pembeli kembali ke keranjang = batal "Beli sekarang"; jangan bocor ke checkout keranjang.
        session()->forget('buy_now');

        $items = CartItem::with('product')->where('user_id', Auth::id())->get();
        $total = $items->sum(fn ($i) => $i->qty * $i->product->final_price);

        return view('cart.index', compact('items', 'total'));
    }

    public function store(Request $request, Product $product)
    {
        if (! $product->is_active) {
    $message = "{$product->title} sudah tidak dijual.";

    return $request->wantsJson()
        ? response()->json(['ok' => false, 'message' => $message], 422)
        : back()->with('error', $message);
}
        $qty = max(1, (int) $request->input('qty', 1));

        $inCart = (int) CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->value('qty');

        if (($inCart + $qty) > $product->stock) {
            $message = "Stok {$product->title} tinggal {$product->stock}.";

            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $item = CartItem::where('user_id', Auth::id())->where('product_id', $product->id)->first();

        if ($item) {
            $item->increment('qty', $qty);
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'qty' => $qty,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'count' => (int) CartItem::where('user_id', Auth::id())->sum('qty'),
                'message' => $product->title.' masuk keranjang!',
            ]);
        }

        return back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['qty' => 'required|integer|min:1|max:50']);

        $stock = (int) ($cartItem->product?->stock ?? 0);

        if ((int) $request->qty > $stock) {
            return back()->with('error', "Stok {$cartItem->product?->title} tinggal {$stock}.");
        }

        $cartItem->update(['qty' => $request->qty]);

        return back()->with('success', 'Jumlah diperbarui.');
    }

    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Item dihapus dari keranjang.');
    }
}
