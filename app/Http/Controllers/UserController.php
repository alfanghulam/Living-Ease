<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user();

        $orders = Order::with('items.product')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $midtrans = app(\App\Services\MidtransService::class);
        if ($midtrans->isConfigured()) {
            foreach ($orders as $order) {
                if ($order->isAwaitingPayment() && $order->midtrans_order_id) {
                    try {
                        $midtrans->syncStatus($order);
                    } catch (\Throwable) {}
                }
            }
        }

        $initial = strtoupper(substr($user->name, 0, 1));
        $avatarPlaceholder = 'data:image/svg+xml;utf8,'.rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">'
            .'<rect width="100" height="100" rx="50" fill="#2563eb"/>'
            .'<text x="50" y="58" font-size="40" fill="white" text-anchor="middle" font-family="Arial" font-weight="bold">'.$initial.'</text>'
            .'</svg>'
        );

        return view('user.profile', compact('user', 'avatarPlaceholder', 'orders'));
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'photo.mimes' => 'Format foto harus JPG, PNG, atau WebP (HEIC belum didukung browser).',
        ]);

        $path = $request->file('photo')->store('avatars', 'public');

        $user = Auth::user();
        $user->profile_photo = $path;
        $user->save();

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}
