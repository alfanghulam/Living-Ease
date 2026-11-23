<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{
    public function show(Order $order, MidtransService $midtrans)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        // Cek status terbaru dari Midtrans
        if ($order->isAwaitingPayment() && $midtrans->isConfigured()) {
            try {
                $midtrans->syncStatus($order);
                $order->refresh();
            } catch (Throwable) {}
        }

        // Pesanan sudah bukan "menunggu bayar", atau Midtrans belum dipasang: alur lama (konfirmasi admin).
        if (! $order->isAwaitingPayment() || ! $midtrans->isConfigured()) {
            return redirect()->route('checkout.success', $order->id);
        }

        try {
            $snapToken = $midtrans->createSnapToken($order);
        } catch (Throwable $e) {
            Log::error('Midtrans: '.$e->getMessage(), ['order' => $order->id]);

            return redirect()->route('checkout.success', $order->id)
                ->with('error', 'Pembayaran online belum bisa dibuka. Pesananmu tetap tersimpan, coba lagi sebentar lagi.');
        }

        return view('checkout.pay', [
            'order' => $order,
            'snapToken' => $snapToken,
            'clientKey' => $midtrans->clientKey(),
            'snapJs' => $midtrans->snapJsUrl(),
        ]);
    }
}
