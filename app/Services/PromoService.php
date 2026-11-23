<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;

/** Satu kode promo global (diatur di Admin > Pengaturan), maksimal sekali pakai per akun. */
class PromoService
{
    public function normalize(?string $code): string
    {
        return strtoupper(trim((string) $code));
    }

    /** Persen diskon efektif (dibatasi 99 supaya total tidak pernah 0, Midtrans menolak Rp 0). */
    public function percent(): int
    {
        return max(0, min(99, (int) Setting::get('promo_percent')));
    }

    public function discountFor(int $subtotal): int
    {
        return (int) floor($subtotal * $this->percent() / 100);
    }

    /** Pesan error kalau kode tidak bisa dipakai pembeli ini, atau null kalau valid / tidak ada kode. */
    public function error(?string $code, int $userId): ?string
    {
        $code = $this->normalize($code);

        if ($code === '') {
            return null;
        }

        $valid = $this->normalize(Setting::get('promo_code'));

        if (Setting::get('promo_active') !== '1' || $valid === '' || $code !== $valid || $this->percent() < 1) {
            return 'Kode promo tidak valid atau sudah tidak berlaku.';
        }

        // Pesanan yang dibatalkan/kedaluwarsa tidak menghanguskan kode.
        $used = Order::where('user_id', $userId)
            ->where('promo_code', $code)
            ->where('status', '!=', 'cancelled')
            ->exists();

        return $used ? 'Kode promo ini sudah pernah kamu pakai.' : null;
    }
}