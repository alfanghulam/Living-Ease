<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExpirePendingOrders extends Command
{
    protected $signature = 'orders:expire';

    protected $description = 'Batalkan pesanan menunggu bayar yang melewati batas waktu dan kembalikan stoknya';

    /** Tenggang tambahan setelah batas waktu, supaya webhook/pembayaran yang mepet sempat masuk. */
    private const GRACE_MINUTES = 5;

    public function handle(MidtransService $midtrans): int
    {
        $minutes = (int) config('shop.order_expire_minutes');

        if ($minutes <= 0) {
            $this->info('Kedaluwarsa pesanan dimatikan (ORDER_EXPIRE_MINUTES=0).');

            return self::SUCCESS;
        }

        $ids = Order::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes($minutes + self::GRACE_MINUTES))
            ->pluck('id');

        $cancelled = 0;

        foreach ($ids as $id) {
            // Tanya Midtrans dulu: jangan batalkan pesanan yang sebenarnya sudah dibayar.
            $order = Order::find($id);

            if ($order && $order->midtrans_order_id) {
                try {
                    $midtrans->syncStatus($order);
                } catch (Throwable $e) {
                    report($e);
                }
            }

            $done = DB::transaction(function () use ($id) {
                $order = Order::whereKey($id)->lockForUpdate()->first();

                if (! $order || $order->status !== 'pending') {
                    return false;
                }

                $order->markCancelled('Kedaluwarsa otomatis: batas waktu pembayaran terlewati.');
                $order->save();

                return true;
            });

            if ($done) {
                $cancelled++;
            }
        }

        $this->info("{$cancelled} pesanan kedaluwarsa dibatalkan.");

        return self::SUCCESS;
    }
}