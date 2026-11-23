@extends('layouts.app')

@section('title', 'Status Pesanan — LivingEase')

@section('content')
    @php
        $canPay = $canPay ?? ($order->isAwaitingPayment() && app(\App\Services\MidtransService::class)->isConfigured());
    @endphp
    <div class="vs-shell vs-success-shell">
        <div class="vs-auth">
            <div class="vs-success-card">
                @if (session('error'))
                    <div class="vs-errors"><span>{{ session('error') }}</span></div>
                @endif

                @if ($order->discount_amount > 0)
                    <div class="vs-discount-badge">
                        Potongan promo {{ $order->promo_code }}: −Rp {{ number_format($order->discount_amount, 0, ',', '.') }}
                    </div>
                @endif

                @if ($order->status === 'paid')
                    <div class="vs-status-icon vs-status-icon--ok">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>

                    <div class="vs-status-tag vs-status-tag--ok">PEMBAYARAN DITERIMA</div>

                    <h1 class="vs-success-title">Pembayaran Diterima</h1>
                    <p class="vs-success-desc">
                        Pesanan <b>#{{ $order->id }}</b> sudah dibayar{{ $order->paymentLabel() ? ' lewat '.$order->paymentLabel() : '' }} dan sedang disiapkan untuk pengiriman.
                    </p>
                @elseif ($canPay)
                    <div class="vs-status-icon vs-status-icon--warn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>

                    <div class="vs-status-tag vs-status-tag--warn">MENUNGGU PEMBAYARAN</div>

                    <h1 class="vs-success-title">Menunggu Pembayaran</h1>
                    <p class="vs-success-desc">
                        Pesanan <b>#{{ $order->id }}</b> sudah dibuat. Silakan selesaikan pembayaran agar pesanan dapat segera diproses.
                    </p>
                @elseif ($order->status === 'cancelled')
                    <div class="vs-status-icon vs-status-icon--err">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </div>

                    <div class="vs-status-tag vs-status-tag--err">PESANAN DIBATALKAN</div>

                    <h1 class="vs-success-title">Pesanan Dibatalkan</h1>
                    <p class="vs-success-desc">Pesanan <b>#{{ $order->id }}</b> tidak jadi diproses. Kamu bisa membuat pesanan baru kapan saja.</p>
                @else
                    <div class="vs-status-icon vs-status-icon--info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 3h12v18l-3-2-3 2-3-2-3 2z"></path>
                            <path d="M9 8h6M9 12h6"></path>
                        </svg>
                    </div>

                    <div class="vs-status-tag vs-status-tag--info">PESANAN DIBUAT</div>

                    <h1 class="vs-success-title">Pesanan Dibuat</h1>
                    <p class="vs-success-desc">
                        Nomor pesanan <b>#{{ $order->id }}</b> · Admin akan segera konfirmasi pembayaran lewat WhatsApp.
                    </p>
                @endif

                {{-- Detail Box (Gaya Admin Card) --}}
                <div class="vs-order-summary-box">
                    <div class="vs-summary-col">
                        <span class="vs-summary-label">NO. PESANAN</span>
                        <b class="vs-summary-val">#{{ $order->id }}</b>
                    </div>
                    <div class="vs-summary-col">
                        <span class="vs-summary-label">TOTAL TAGIHAN</span>
                        <b class="vs-summary-val vs-summary-val--total">Rp {{ number_format($order->total, 0, ',', '.') }}</b>
                    </div>
                    @if ($order->paymentLabel() || $order->channelLabel())
                        <div class="vs-summary-col">
                            <span class="vs-summary-label">METODE</span>
                            <b class="vs-summary-val">{{ $order->paymentLabel() ?? $order->channelLabel() }}</b>
                        </div>
                    @endif
                </div>

                <div class="vs-actions vs-actions--center">
                    @if ($canPay)
                        <a href="{{ route('payment.show', $order->id) }}" class="vs-btn vs-btn--primary">Bayar sekarang</a>
                        <a href="{{ route('checkout.success', $order->id) }}" class="vs-btn vs-btn--line">Cek status</a>
                        <a href="{{ route('user.profile') }}" class="vs-btn vs-btn--line">Lihat pesanan</a>
                    @else
                        <a href="{{ route('home') }}" class="vs-btn vs-btn--primary">Lanjut belanja</a>
                        <a href="{{ route('user.profile') }}" class="vs-btn vs-btn--line">Lihat pesanan</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
/* =========================================================
   SUCCESS / ORDER STATUS — ADMIN PANEL UI STYLE
   ========================================================= */

.vs-success-shell {
    min-height: 75vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 16px;
}

.vs-auth {
    width: 100%;
    max-width: 520px;
    margin: 0 auto;
}

.vs-success-card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 18px;
    padding: 40px 32px;
    box-shadow: var(--shadow-2);
    text-align: center;
    position: relative;
    box-sizing: border-box;
}

.vs-status-icon {
    width: 58px;
    height: 58px;
    border-radius: 16px;
    display: grid;
    place-items: center;
    margin: 0 auto 16px;
    box-shadow: var(--shadow-sm);
}

.vs-status-icon svg {
    width: 28px;
    height: 28px;
}

.vs-status-icon--ok {
    background: color-mix(in srgb, var(--ok) 12%, var(--surface));
    color: var(--ok);
    border: 1px solid color-mix(in srgb, var(--ok) 35%, var(--line));
}

.vs-status-icon--warn {
    background: color-mix(in srgb, var(--warn) 12%, var(--surface));
    color: var(--warn);
    border: 1px solid color-mix(in srgb, var(--warn) 35%, var(--line));
}

.vs-status-icon--err {
    background: color-mix(in srgb, var(--danger) 12%, var(--surface));
    color: var(--danger);
    border: 1px solid color-mix(in srgb, var(--danger) 35%, var(--line));
}

.vs-status-icon--info {
    background: var(--surface-2);
    color: var(--ink);
    border: 1px solid var(--line);
}

.vs-status-tag {
    display: inline-block;
    font-family: var(--font-display);
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 1.4px;
    border-radius: 99px;
    padding: 3px 12px;
    margin-bottom: 12px;
}

.vs-status-tag--ok {
    color: var(--ok);
    background: color-mix(in srgb, var(--ok) 10%, var(--surface));
    border: 1px solid color-mix(in srgb, var(--ok) 25%, var(--line));
}

.vs-status-tag--warn {
    color: var(--warn);
    background: color-mix(in srgb, var(--warn) 10%, var(--surface));
    border: 1px solid color-mix(in srgb, var(--warn) 25%, var(--line));
}

.vs-status-tag--err {
    color: var(--danger);
    background: color-mix(in srgb, var(--danger) 10%, var(--surface));
    border: 1px solid color-mix(in srgb, var(--danger) 25%, var(--line));
}

.vs-status-tag--info {
    color: var(--ink-soft);
    background: var(--surface-2);
    border: 1px solid var(--line);
}

.vs-success-title {
    font-family: var(--font-display);
    font-size: 24px;
    font-weight: 800;
    letter-spacing: -0.6px;
    color: var(--ink);
    margin: 0 0 10px;
}

.vs-success-desc {
    font-size: 13.5px;
    color: var(--ink-soft);
    line-height: 1.6;
    margin: 0 auto 24px;
    max-width: 420px;
}

.vs-order-summary-box {
    background: var(--surface-2);
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: 14px 18px;
    display: flex;
    justify-content: space-around;
    align-items: center;
    gap: 12px;
    margin-bottom: 28px;
    flex-wrap: wrap;
}

.vs-summary-col {
    display: flex;
    flex-direction: column;
    gap: 3px;
    text-align: center;
}

.vs-summary-label {
    font-family: var(--font-display);
    font-size: 8.5px;
    font-weight: 700;
    letter-spacing: 1.2px;
    color: var(--ink-mute);
    text-transform: uppercase;
}

.vs-summary-val {
    font-size: 13px;
    color: var(--ink);
    font-weight: 600;
}

.vs-summary-val--total {
    font-family: var(--font-display);
    font-weight: 800;
    color: var(--ink);
}

.vs-discount-badge {
    display: inline-block;
    font-size: 11px;
    color: var(--ok);
    background: color-mix(in srgb, var(--ok) 10%, var(--surface));
    border: 1px solid color-mix(in srgb, var(--ok) 25%, var(--line));
    border-radius: 8px;
    padding: 4px 10px;
    margin-bottom: 16px;
}

.vs-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
}

.vs-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-display);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    border-radius: 10px;
    padding: 12px 22px;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s cubic-bezier(0.16, 1, 0.3, 1);
    box-sizing: border-box;
}

.vs-btn--primary {
    background: var(--ink);
    color: var(--bg);
    border: 1px solid var(--ink);
    box-shadow: var(--shadow-sm);
}

.vs-btn--primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-accent);
    color: var(--bg);
}

.vs-btn--primary:active {
    transform: translateY(0);
}

.vs-btn--line {
    background: var(--surface);
    color: var(--ink);
    border: 1px solid var(--line);
}

.vs-btn--line:hover {
    background: var(--surface-2);
    border-color: var(--line-strong);
    color: var(--ink);
}

@media (max-width: 480px) {
    .vs-success-card {
        padding: 28px 18px;
    }
    .vs-actions {
        flex-direction: column;
    }
    .vs-btn {
        width: 100%;
    }
}
</style>
@endpush
