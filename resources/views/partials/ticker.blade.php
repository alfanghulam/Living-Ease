@php
    $payOnline = app(\App\Services\MidtransService::class)->isConfigured();
@endphp

<div class="vs-notice">
    <div class="vs-shell">
        <span>Dikirim dari {{ $storeSettings['store_address'] ?? 'Yogyakarta, Indonesia' }}</span>
        <span>
            {{ $payOnline
                ? 'Bayar lewat QRIS, transfer bank, e-wallet, atau kartu'
                : 'Pembayaran dikonfirmasi admin lewat WhatsApp' }}
        </span>
    </div>
</div>
