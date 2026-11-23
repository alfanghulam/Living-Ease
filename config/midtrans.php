<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Dapatkan Server Key & Client Key dari Dashboard Midtrans
    | (Settings → Access Keys). Gunakan Sandbox untuk development.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),

    // Prefix order_id yang dikirim ke Midtrans (hindari bentrok)
    'order_prefix' => env('MIDTRANS_ORDER_PREFIX', 'VS-'),
    // Daftar channel yang ditampilkan di Snap, dipisah koma. Kosong = semua channel yang aktif di dashboard.
'enabled_payments' => array_values(array_filter(array_map('trim', explode(',', (string) env('MIDTRANS_ENABLED_PAYMENTS', ''))))),
'channels' => [
    'Transfer bank' => [
        'bca_va'     => ['label' => 'BCA Virtual Account', 'icon' => 'img/pay/bca.svg'],
        'bni_va'     => ['label' => 'BNI Virtual Account', 'icon' => 'img/pay/bni.svg'],
        'bri_va'     => ['label' => 'BRI Virtual Account', 'icon' => 'img/pay/bri.svg'],
        'permata_va' => ['label' => 'Permata Virtual Account', 'icon' => 'img/pay/permata.svg'],
        'echannel'   => ['label' => 'Mandiri Bill Payment', 'icon' => 'img/pay/mandiri.svg'],
    ],
    'E-wallet' => [
        'gopay'     => ['label' => 'GoPay', 'icon' => 'img/pay/gopay.svg'],
        'shopeepay' => ['label' => 'ShopeePay', 'icon' => 'img/pay/shopeepay.svg'],
    ],
    'QRIS' => [
        'other_qris' => ['label' => 'QRIS', 'icon' => 'img/pay/qris.svg'],
    ],
],
];
