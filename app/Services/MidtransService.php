<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Integrasi Midtrans Snap lewat REST API (tanpa paket tambahan).
 * Dokumen: https://docs.midtrans.com/reference/snap-api
 */
class MidtransService
{
    public function isConfigured(): bool
    {
        return filled(config('midtrans.server_key')) && filled(config('midtrans.client_key'));
    }

        /** Channel yang boleh dipilih pembeli: dari config, disaring MIDTRANS_ENABLED_PAYMENTS kalau diisi. */
    public function availableChannels(): array
    {
        $whitelist = (array) config('midtrans.enabled_payments', []);
        $groups = [];

        foreach ((array) config('midtrans.channels', []) as $group => $options) {
            $options = $whitelist ? array_intersect_key($options, array_flip($whitelist)) : $options;

            if ($options) {
                $groups[$group] = $options;
            }
        }

        return $groups;
    }

    public function clientKey(): string
    {
        return (string) config('midtrans.client_key');
    }

    public function snapJsUrl(): string
    {
        return $this->production()
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    protected function production(): bool
    {
        return filter_var(config('midtrans.is_production'), FILTER_VALIDATE_BOOLEAN);
    }

    protected function snapBase(): string
    {
        return $this->production()
            ? 'https://app.midtrans.com/snap/v1'
            : 'https://app.sandbox.midtrans.com/snap/v1';
    }

    protected function apiBase(): string
    {
        return $this->production()
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    protected function client(): PendingRequest
    {
        return Http::withBasicAuth((string) config('midtrans.server_key'), '')
            ->acceptJson()
            ->asJson()
            ->timeout(20);
    }

    /**
     * Buat transaksi Snap dan kembalikan snap_token.
     * Token lama dipakai ulang selama pesanan masih menunggu bayar.
     */
    public function createSnapToken(Order $order): string
    {
        if ($order->snap_token && $order->midtrans_order_id && $order->status === 'pending') {
            return $order->snap_token;
        }

        $order->loadMissing('items');

        $midtransOrderId = config('midtrans.order_prefix', 'VS-').$order->id.'-'.time();
        $itemDetails = $order->items->map(fn ($item) => [
                'id' => (string) $item->product_id,
                'price' => (int) $item->price,
                'quantity' => (int) $item->qty,
                'name' => mb_substr($item->title(), 0, 50),
            ])->values()->all();

            // Jumlah item_details harus sama dengan gross_amount, jadi potongan promo dikirim sebagai baris bernilai negatif.
            if ((int) $order->discount_amount > 0) {
                $itemDetails[] = [
                    'id' => 'PROMO',
                    'price' => -1 * (int) $order->discount_amount,
                    'quantity' => 1,
                    'name' => mb_substr('Diskon '.$order->promo_code, 0, 50),
                ];
            }

            // Tambahkan ongkos kirim ke item_details agar total gross_amount Midtrans cocok.
            if ((int) $order->shipping_fee > 0) {
                $itemDetails[] = [
                    'id' => 'SHIPPING',
                    'price' => (int) $order->shipping_fee,
                    'quantity' => 1,
                    'name' => mb_substr('Ongkir ('.($order->courier ?: 'Kurir').')', 0, 50),
                ];
            }

        $params = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => (int) $order->total,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $order->full_name,
                'email' => $order->email,
                'shipping_address' => [
                    'first_name' => $order->full_name,
                    'address' => $order->address,
                    'city' => $order->city,
                    'postal_code' => $order->zip,
                    'country_code' => 'IDN',
                ],
            ],
            'callbacks' => ['finish' => route('checkout.success', $order->id)],
        ];

        $expireMinutes = (int) config('shop.order_expire_minutes');

        if ($expireMinutes > 0) {
            $params['expiry'] = [
                'start_time' => $order->created_at->copy()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s O'),
                'unit' => 'minutes',
                'duration' => max(5, $expireMinutes),
            ];
        }

        $enabled = $order->payment_channel ? [$order->payment_channel] : config('midtrans.enabled_payments', []);

        if (! empty($enabled)) {
            $params['enabled_payments'] = $enabled;
        }

        $response = $this->client()->post($this->snapBase().'/transactions', $params);

        if ($response->failed() || blank($response->json('token'))) {
            Log::warning('Midtrans: gagal membuat transaksi', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            throw new RuntimeException('Midtrans menolak permintaan pembayaran. Cek Server Key dan mode sandbox/production.');
        }

        $order->forceFill([
            'midtrans_order_id' => $midtransOrderId,
            'snap_token' => $response->json('token'),
        ])->save();

        return $order->snap_token;
    }

    /** signature_key = sha512(order_id + status_code + gross_amount + server_key) */
    public function verifySignature(array $payload): bool
    {
        $expected = hash('sha512',
            ($payload['order_id'] ?? '').
            ($payload['status_code'] ?? '').
            ($payload['gross_amount'] ?? '').
            config('midtrans.server_key')
        );

        return hash_equals($expected, (string) ($payload['signature_key'] ?? ''));
    }

    /** Ambil status terbaru langsung dari Midtrans (dipakai saat webhook tidak bisa masuk, mis. localhost). */
    public function syncStatus(Order $order): void
    {
        if (! $this->isConfigured() || ! $order->midtrans_order_id) {
            return;
        }

        $response = $this->client()->get($this->apiBase().'/'.$order->midtrans_order_id.'/status');

        // 404 = pembeli belum memilih metode bayar; bukan error.
        if ($response->failed() || blank($response->json('transaction_status'))) {
            return;
        }

        $this->applyPayload($response->json(), verify: false);
    }

    /**
     * Terapkan notifikasi Midtrans ke pesanan.
     *
     * @return array{order: Order|null, result: string}
     */
    public function applyPayload(array $payload, bool $verify = true): array
    {
        if ($verify && ! $this->verifySignature($payload)) {
            return ['order' => null, 'result' => 'invalid_signature'];
        }

        $orderId = (string) ($payload['order_id'] ?? '');
        $order = Order::where('midtrans_order_id', $orderId)->first();

        if (! $order && preg_match('/^'.preg_quote(config('midtrans.order_prefix', 'VS-'), '/').'(\d+)-/', $orderId, $m)) {
            $order = Order::find($m[1]);
        }

        if (! $order) {
            return ['order' => null, 'result' => 'not_found'];
        }

        // Nominal harus sama dengan pesanan, kalau tidak, abaikan.
        if (isset($payload['gross_amount']) && (int) round((float) $payload['gross_amount']) !== (int) $order->total) {
            Log::warning('Midtrans: nominal tidak cocok', ['order' => $order->id, 'payload' => $payload]);

            return ['order' => $order, 'result' => 'amount_mismatch'];
        }

        $transaction = (string) ($payload['transaction_status'] ?? '');
        $fraud = $payload['fraud_status'] ?? null;

        $target = match (true) {
            $transaction === 'settlement',
            $transaction === 'capture' && in_array($fraud, [null, 'accept'], true) => 'paid',
            in_array($transaction, ['cancel', 'deny', 'expire', 'failure'], true) => 'cancelled',
            default => null,
        };

        DB::transaction(function () use (&$order, $target, $payload, $transaction) {
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            if (! empty($payload['payment_type'])) {
                $order->payment_type = $payload['payment_type'];
            }
            if (! empty($payload['transaction_id'])) {
                $order->midtrans_transaction_id = $payload['transaction_id'];
            }

            if ($target && $order->canTransitionTo($target)) {
                $target === 'paid'
                    ? $order->markPaid()
                    : $order->markCancelled("Dibatalkan otomatis oleh Midtrans ({$transaction}).");
            }

            $order->save();
        });

        return ['order' => $order, 'result' => $target ?? 'pending'];
    }
}
