@extends('layouts.app')

@section('title', 'Pembayaran — LivingEase')

@section('content')
    <div class="vs-shell vs-pay-shell">
        <div class="vs-auth">
            <div class="vs-pay-window-card">
                <div class="vs-status-icon vs-status-icon--pay">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="5" width="20" height="14" rx="3"></rect>
                        <line x1="2" y1="10" x2="22" y2="10"></line>
                    </svg>
                </div>

                <div class="vs-status-tag vs-status-tag--pay">GATEWAY PEMBAYARAN</div>

                <h1 class="vs-pay-heading">Pembayaran Pesanan</h1>
                <p class="vs-pay-sub">
                    Pesanan <b>#{{ $order->id }}</b> senilai <b>Rp {{ number_format($order->total, 0, ',', '.') }}</b>.
                    @if ($order->channelLabel())
                        Selesaikan pembayaran lewat <b>{{ $order->channelLabel() }}</b> di jendela Midtrans yang muncul.
                    @else
                        Pilih metode pembayaran (QRIS, VA, E-Wallet) pada jendela Midtrans.
                    @endif
                </p>

                <div class="vs-order-summary-box">
                    <div class="vs-summary-col">
                        <span class="vs-summary-label">NO. PESANAN</span>
                        <b class="vs-summary-val">#{{ $order->id }}</b>
                    </div>
                    <div class="vs-summary-col">
                        <span class="vs-summary-label">TOTAL BAYAR</span>
                        <b class="vs-summary-val vs-summary-val--total">Rp {{ number_format($order->total, 0, ',', '.') }}</b>
                    </div>
                    @if ($order->channelLabel())
                        <div class="vs-summary-col">
                            <span class="vs-summary-label">CHANNEL</span>
                            <b class="vs-summary-val">{{ $order->channelLabel() }}</b>
                        </div>
                    @endif
                </div>

                <p class="vs-pay-msg" id="pay-msg" role="status"></p>

                <div class="vs-actions vs-actions--center">
                    <button type="button" class="vs-btn vs-btn--primary" id="pay-btn">Buka jendela pembayaran</button>
                    <a href="{{ route('user.profile') }}" class="vs-btn vs-btn--line">Bayar nanti</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
/* =========================================================
   PAYMENT SCREEN — ADMIN PANEL UI STYLE
   ========================================================= */

.vs-pay-shell {
    min-height: 75vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 16px;
}

.vs-pay-window-card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 18px;
    padding: 40px 32px;
    box-shadow: var(--shadow-2);
    text-align: center;
    position: relative;
    box-sizing: border-box;
}

.vs-status-icon--pay {
    width: 58px;
    height: 58px;
    border-radius: 16px;
    display: grid;
    place-items: center;
    margin: 0 auto 16px;
    background: var(--surface-2);
    color: var(--ink);
    border: 1px solid var(--line);
    box-shadow: var(--shadow-sm);
}

.vs-status-icon--pay svg {
    width: 28px;
    height: 28px;
}

.vs-status-tag--pay {
    display: inline-block;
    font-family: var(--font-display);
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 1.4px;
    border-radius: 99px;
    padding: 3px 12px;
    margin-bottom: 12px;
    color: var(--ink);
    background: var(--surface-2);
    border: 1px solid var(--line);
}

.vs-pay-heading {
    font-family: var(--font-display);
    font-size: 24px;
    font-weight: 800;
    letter-spacing: -0.6px;
    color: var(--ink);
    margin: 0 0 10px;
}

.vs-pay-sub {
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
    margin-bottom: 24px;
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

.vs-pay-msg {
    font-size: 13px;
    color: var(--danger);
    margin-bottom: 16px;
    min-height: 20px;
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
    .vs-pay-window-card {
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

@push('scripts')
    <script src="{{ $snapJs }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        (function () {
            const msg = document.getElementById('pay-msg');
            const finish = () => { window.location.href = @json(route('checkout.success', $order->id)); };

            function pay() {
                if (!window.snap) { msg.textContent = 'Jendela pembayaran belum termuat. Periksa koneksi internet lalu coba lagi.'; return; }
                msg.textContent = '';
                window.snap.pay(@json($snapToken), {
                    onSuccess: finish,
                    onPending: finish,
                    onError: function () { msg.textContent = 'Pembayaran gagal diproses. Silakan coba lagi.'; },
                    onClose: function () { msg.textContent = 'Jendela pembayaran ditutup. Pesananmu tetap tersimpan dan bisa dibayar kapan saja.'; }
                });
            }

            document.getElementById('pay-btn').addEventListener('click', pay);
            window.addEventListener('load', pay);
        })();
    </script>
@endpush
