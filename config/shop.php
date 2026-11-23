<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Batas waktu bayar pesanan (menit)
    |--------------------------------------------------------------------------
    | Stok dikurangi saat pesanan dibuat dan ditahan sampai pesanan dibayar.
    | Pesanan "menunggu bayar" yang melewati batas ini dibatalkan otomatis oleh
    | `php artisan orders:expire` dan stoknya dikembalikan.
    |
    | Isi 0 untuk mematikan kedaluwarsa otomatis (mis. kalau toko memakai
    | konfirmasi manual lewat WhatsApp). Kalau dipakai bersama Midtrans, batas
    | ini juga dikirim sebagai masa berlaku transaksi (minimal 5 menit).
    */
    'order_expire_minutes' => (int) env('ORDER_EXPIRE_MINUTES', 60),
    // Hanya dipakai oleh `php artisan db:seed`. Kosongkan setelah akun admin dibuat.
'admin_email' => env('ADMIN_EMAIL'),
'admin_password' => env('ADMIN_PASSWORD'),
];