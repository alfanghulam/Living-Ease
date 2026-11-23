@extends('layouts.app')

@section('title', 'Checkout — LivingEase')

@section('content')
    <div class="vs-shell">
        <div class="vs-sechead vs-section--first vs-reveal">
            <div>
                <h2>Checkout</h2>
                <p>{{ $items->count() }} produk · lengkapi alamat pengiriman</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="vs-errors">
                @foreach ($errors->all() as $error)
                    <span>{{ $error }}</span>
                @endforeach
            </div>
        @endif

        <div class="vs-checkout">
            <form class="vs-panel" method="POST" action="{{ route('checkout.store') }}">
                @csrf

                <h2>Alamat pengiriman</h2>

                <div class="vs-form-grid">
                    <div class="vs-field vs-field--full">
                        <label for="full_name">Nama lengkap</label>
                        <input id="full_name" class="vs-input" type="text" name="full_name"
                               value="{{ old('full_name', auth()->user()->name) }}" required>
                    </div>

                    <div class="vs-field vs-field--full">
                        <label for="email">Email</label>
                        <input id="email" class="vs-input" type="email" name="email"
                               value="{{ old('email', auth()->user()->email) }}" required>
                    </div>

                    <div class="vs-field vs-field--full">
                        <label for="address">Alamat</label>
                        <input id="address" class="vs-input" type="text" name="address"
                               value="{{ old('address') }}" placeholder="Jalan, nomor rumah, RT/RW" required>
                    </div>

                    <div class="vs-field">
                        <label for="city">Kota / Kabupaten</label>
                        <input id="city" class="vs-input" type="text" name="city" value="{{ old('city') }}" required>
                    </div>

                    <div class="vs-field">
                        <label for="state">Provinsi</label>
                        <input id="state" class="vs-input" type="text" name="state" value="{{ old('state') }}" required>
                    </div>

                    <div class="vs-field vs-field--full">
                        <label for="phone">Nomor HP penerima</label>
                        <input id="phone" class="vs-input" type="tel" name="phone" inputmode="tel"
                               value="{{ old('phone', $lastPhone ?? '') }}" placeholder="081234567890" required>
                        <small class="vs-hint">Dipakai kurir untuk menghubungimu saat mengantar.</small>
                    </div>

                    <div class="vs-field vs-field--full">
                        <label for="zip">Kode pos</label>
                        <input id="zip" class="vs-input" type="text" name="zip" value="{{ old('zip') }}" required>
                    </div>
                </div>

                {{-- Hidden flag for shipping requirement --}}
                <input type="hidden" name="needs_shipping" value="{{ empty($rates) ? 0 : 1 }}">

                {{-- Pilihan Kurir & Layanan --}}
                <div class="vs-field vs-field--full">
                    <label for="courier">Kurir</label>
                    <select id="courier" class="vs-input" name="courier">
                        <option value="">Pilih kurir</option>
                        @foreach(collect($rates ?? [])->pluck('courier')->unique() as $courierName)
                            <option value="{{ $courierName }}">{{ $courierName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="vs-field vs-field--full">
                    <label for="service_code">Layanan</label>
                    <select id="service_code" class="vs-input" name="service_code" disabled required>
                        <option value="">Pilih layanan</option>
                    </select>
                </div>

                <div class="vs-field vs-field--full">
                    <label for="shipping_fee">Ongkir (IDR)</label>
                    <input id="shipping_fee" class="vs-input" type="number" name="shipping_fee" value="{{ old('shipping_fee') }}"
                           min="0" step="0.01" placeholder="0" readonly>
                </div>

                <h2 class="vs-pay-title">Metode Pembayaran</h2>
                <div class="vs-pay-list">
                    @foreach ($channels as $group => $options)
                        @php
                            $isQris = strtolower(trim($group)) === 'qris';
                            $isBank = str_contains(strtolower($group), 'bank') || str_contains(strtolower($group), 'transfer');
                            $isWallet = str_contains(strtolower($group), 'wallet') || str_contains(strtolower($group), 'dompet');
                            $groupKeys = array_keys($options);
                            $groupHasSelected = in_array(old('payment_channel'), $groupKeys, true);

                            if ($isQris) {
                                $headerBadges = ['img/pay/qris.svg', 'img/pay/gopay.svg', 'img/pay/shopeepay.svg', 'img/pay/dana.svg', 'img/pay/ovo.svg', 'img/pay/linkaja.svg'];
                                $groupTitle = 'QRIS';
                            } elseif ($isBank) {
                                $headerBadges = ['img/pay/atm-bersama.svg', 'img/pay/prima.svg', 'img/pay/alto.svg', 'img/pay/bca.svg', 'img/pay/bni.svg', 'img/pay/bri.svg', 'img/pay/mandiri.svg'];
                                $groupTitle = 'TRANSFER BANK (VA)';
                            } elseif ($isWallet) {
                                $headerBadges = ['img/pay/gopay.svg', 'img/pay/shopeepay.svg', 'img/pay/dana.svg', 'img/pay/ovo.svg', 'img/pay/linkaja.svg'];
                                $groupTitle = 'E-WALLET';
                            } else {
                                $headerBadges = [];
                                foreach ($options as $c) {
                                    if (is_array($c) && !empty($c['icon'])) $headerBadges[] = $c['icon'];
                                }
                                $groupTitle = strtoupper($group);
                            }
                        @endphp

                        @if ($isQris && count($options) === 1)
                            @php
                                $qrisKey = array_key_first($options);
                                $qrisData = $options[$qrisKey];
                                $qrisLabel = is_array($qrisData) ? ($qrisData['label'] ?? 'QRIS') : $qrisData;
                            @endphp
                            <div class="vs-pay-card {{ old('payment_channel') === $qrisKey ? 'is-selected' : '' }}">
                                <label class="vs-pay-card__header vs-pay-card__header--label">
                                    <div class="vs-pay-card__logos">
                                        @foreach ($headerBadges as $badge)
                                            <img src="{{ asset($badge) }}" alt="" class="vs-pay-badge">
                                        @endforeach
                                    </div>
                                    <div class="vs-pay-card__title">
                                        <input type="radio" name="payment_channel" value="{{ $qrisKey }}"
                                               @checked(old('payment_channel') === $qrisKey) required class="vs-pay-radio">
                                        <span>{{ $qrisLabel }}</span>
                                        <span class="vs-pay-arrow">›</span>
                                    </div>
                                </label>
                            </div>
                        @else
                            <details class="vs-pay-card vs-pay-accordion {{ $groupHasSelected ? 'is-selected' : '' }}"
                                     {{ ($groupHasSelected || $loop->first) ? 'open' : '' }}>
                                <summary class="vs-pay-card__header">
                                    <div class="vs-pay-card__logos">
                                        @foreach ($headerBadges as $badge)
                                            <img src="{{ asset($badge) }}" alt="" class="vs-pay-badge">
                                        @endforeach
                                    </div>
                                    <div class="vs-pay-card__title">
                                        <span>{{ $groupTitle }}</span>
                                        <span class="vs-pay-arrow">›</span>
                                    </div>
                                </summary>
                                <div class="vs-pay-card__body">
                                    @foreach ($options as $value => $channel)
                                        @php
                                            // Ensure $label is always a string (fallback to $value if needed)
                                            if (is_array($channel)) {
                                                $label = $channel['label'] ?? $value;
                                                $icon  = $channel['icon'] ?? null;
                                            } elseif (is_object($channel)) {
                                                $label = $channel->label ?? $value;
                                                $icon  = $channel->icon ?? null;
                                            } else {
                                                $label = $channel;
                                                $icon  = null;
                                            }
                                        @endphp
                                        <label class="vs-pay-option {{ old('payment_channel') === $value ? 'is-active' : '' }}">
                                            <input type="radio" name="payment_channel" value="{{ $value }}"
                                                   @checked(old('payment_channel') === $value) required class="vs-pay-radio">
                                            @if ($icon)
                                                <img src="{{ asset($icon) }}" alt="{{ $label }}" class="vs-pay-option__icon">
                                            @endif
                                            <span class="vs-pay-option__name">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </details>
                        @endif
                    @endforeach
                </div>

                <button type="submit" class="vs-btn vs-btn--primary vs-btn--block">Buat pesanan</button>

                <p class="vs-hint">
                    Setelah menekan Buat pesanan, kamu langsung diarahkan ke pembayaran dengan metode yang dipilih.
                    @if (config('shop.order_expire_minutes') > 0)
                        Pesanan dibatalkan otomatis kalau tidak dibayar dalam {{ config('shop.order_expire_minutes') }} menit.
                    @else
                        Setelah pesanan dibuat, admin akan konfirmasi pembayaran lewat WhatsApp.
                    @endif
                </p>
            </form>

            <aside class="vs-panel">
                <h2>Ringkasan pesanan</h2>
                <div class="vs-order__items">
                    @foreach ($items as $item)
                        <div class="vs-order__item">
                            <span class="vs-order__thumb">
                                <img src="{{ $item->product->image_url }}" alt=""
                                     onerror="this.onerror=null;this.src='{{ asset('img/no-image.png') }}'">
                            </span>
                            <span>
                                <b>{{ $item->product->title }}</b><br>
                                <small>{{ $item->qty }} × Rp {{ number_format($item->product->final_price, 0, ',', '.') }}</small>
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="vs-order__row"><span>Subtotal</span><b>Rp {{ number_format($total, 0, ',', '.') }}</b></div>
                <div class="vs-order__row" id="shipping-row" style="display:none;">
                    <span>Ongkir ({{ $selectedCourier ?? 'kurir' }})</span>
                    <b id="shipping-fee">Rp 0</b>
                </div>
                <div class="vs-order__total"><span>Total</span><span id="order-grand-total">Rp {{ number_format($total, 0, ',', '.') }}</span></div>
                <a href="{{ route('cart.index') }}" class="vs-btn vs-btn--line vs-btn--block">Kembali ke keranjang</a>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const rates = @json($rates);
    const baseSubtotal = {{ (int) $total }};
    const courierSelect = document.getElementById('courier');
    const serviceSelect = document.getElementById('service_code');
    const feeInput = document.getElementById('shipping_fee');
    const feeDisplay = document.getElementById('shipping-fee');
    const shippingRow = document.getElementById('shipping-row');
    const shippingLabel = shippingRow.querySelector('span');
    const grandTotalDisplay = document.getElementById('order-grand-total');

    courierSelect.addEventListener('change', function () {
        const selectedCourier = this.value;
        serviceSelect.innerHTML = '<option value="">Pilih layanan</option>';
        serviceSelect.disabled = true;
        updateShipping(0);

        if (!selectedCourier) {
            shippingLabel.textContent = 'Ongkir (kurir)';
            return;
        }

        const filtered = rates.filter(r => r.courier === selectedCourier);
        filtered.forEach(r => {
            const opt = document.createElement('option');
            opt.value = r.service_code;
            opt.textContent = `${r.service_code} – ${r.estimated} (Rp ${Number(r.cost).toLocaleString('id-ID')})`;
            opt.dataset.cost = r.cost;
            serviceSelect.appendChild(opt);
        });

        if (filtered.length > 0) {
            serviceSelect.disabled = false;
            // Otomatis pilih layanan pertama agar user tidak bingung
            serviceSelect.selectedIndex = 1;
            updateShipping(filtered[0].cost);
        }

        shippingLabel.textContent = `Ongkir (${selectedCourier})`;
    });

    serviceSelect.addEventListener('change', function () {
        const cost = this.selectedOptions[0] ? this.selectedOptions[0].dataset.cost : 0;
        updateShipping(cost);
    });

    function updateShipping(cost) {
        cost = Number(cost) || 0;
        feeInput.value = cost > 0 ? cost : '';
        feeDisplay.textContent = 'Rp ' + cost.toLocaleString('id-ID');
        shippingRow.style.display = cost > 0 ? 'flex' : 'none';
        if (grandTotalDisplay) {
            grandTotalDisplay.textContent = 'Rp ' + (baseSubtotal + cost).toLocaleString('id-ID');
        }
    }

    // Jika sudah ada pilihan kurir sebelumnya (misal saat back/validation)
    if (courierSelect.value) {
        courierSelect.dispatchEvent(new Event('change'));
    }
</script>
@endpush

@push('styles')
<style>
/* =========================================================
   CHECKOUT — ADMIN PANEL DESIGN & INTERACTION SYSTEM
   Sama dengan UI/UX Admin Panel (Plus Jakarta Sans, warna, panel, kartu)
   ========================================================= */

.vs-shell {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px 20px 60px;
}

.vs-sechead {
    margin-bottom: 24px;
}

.vs-sechead h2 {
    font-family: var(--font-display);
    font-size: clamp(24px, 3.2vw, 32px);
    font-weight: 800;
    letter-spacing: -0.8px;
    color: var(--ink);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.vs-sechead p {
    font-size: 13px;
    color: var(--ink-soft);
    margin: 6px 0 0;
}

.vs-errors {
    background: color-mix(in srgb, var(--danger) 10%, var(--surface));
    border: 1px solid color-mix(in srgb, var(--danger) 35%, var(--line));
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 22px;
    color: var(--danger);
    font-size: 13px;
    font-weight: 500;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

/* Layout Grid */
.vs-checkout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 380px;
    gap: 24px;
    align-items: start;
}

@media (max-width: 960px) {
    .vs-checkout {
        grid-template-columns: 1fr;
    }
}

/* Panel Card (Mengadopsi .ad-panel dari Admin) */
.vs-panel {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 16px;
    padding: 26px 28px;
    box-shadow: var(--shadow-1);
    transition: border-color .2s ease;
}

.vs-panel h2 {
    font-family: var(--font-display);
    font-size: 12.5px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: var(--ink);
    margin: 0 0 18px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.vs-panel h2::before {
    content: "";
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--ink);
}

/* Form Grid & Input Fields (Mengadopsi .ad-form & .vs-input dari Admin) */
.vs-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px 14px;
    margin-bottom: 22px;
}

.vs-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.vs-field--full {
    grid-column: 1 / -1;
}

.vs-field label {
    font-family: var(--font-display);
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 1.4px;
    text-transform: uppercase;
    color: var(--ink-soft);
}

.vs-input {
    width: 100%;
    padding: 11px 14px;
    font-size: 13.5px;
    font-family: inherit;
    color: var(--ink);
    background: var(--surface-2);
    border: 1px solid var(--line);
    border-radius: 10px;
    box-sizing: border-box;
    transition: all .18s ease;
}

.vs-input:hover {
    border-color: var(--line-strong);
}

.vs-input:focus {
    background: var(--surface);
    border-color: var(--ink);
    outline: none;
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--ink) 9%, transparent);
}

.vs-input:read-only {
    background: var(--surface-3);
    color: var(--ink-soft);
    cursor: not-allowed;
    border-color: var(--line);
}

select.vs-input {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 16px;
    padding-right: 42px;
    cursor: pointer;
}

.vs-hint {
    font-size: 11px;
    color: var(--ink-mute);
    line-height: 1.4;
    margin-top: 2px;
}

/* Payment Method List (Mengadopsi .ad-stat dari Admin) */
.vs-pay-title {
    margin-top: 28px !important;
    padding-top: 20px;
    border-top: 1px solid var(--line);
}

.vs-pay-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 14px;
    margin-bottom: 24px;
}

.vs-pay-card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 14px;
    overflow: hidden;
    transition: all .2s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: var(--shadow-sm);
}

.vs-pay-card:hover {
    border-color: var(--line-strong);
    box-shadow: var(--shadow-1);
    transform: translateY(-1px);
}

.vs-pay-card.is-selected {
    border-color: var(--ink);
    background: var(--surface);
    box-shadow: 0 0 0 1.5px var(--ink);
}

.vs-pay-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    cursor: pointer;
    user-select: none;
    list-style: none;
    gap: 12px;
    text-decoration: none;
    background: var(--surface);
}

.vs-pay-card__header::-webkit-details-marker {
    display: none;
}

.vs-pay-card__header--label {
    margin: 0;
    width: 100%;
}

.vs-pay-card__logos {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    max-width: 65%;
}

.vs-pay-badge {
    height: 22px;
    width: auto;
    max-width: 52px;
    object-fit: contain;
    border-radius: 6px;
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.08);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    display: inline-block;
    padding: 2px 4px;
}

.vs-pay-card__title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: var(--font-display);
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 0.8px;
    color: var(--ink);
    white-space: nowrap;
    text-transform: uppercase;
}

.vs-pay-arrow {
    font-size: 18px;
    line-height: 1;
    color: var(--ink-mute);
    transition: transform 0.2s ease;
    display: inline-block;
}

details[open] > summary .vs-pay-arrow {
    transform: rotate(90deg);
}

.vs-pay-card__body {
    padding: 12px 14px 14px;
    background: var(--surface-2);
    border-top: 1px solid var(--line);
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.vs-pay-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 16px;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 10px;
    cursor: pointer;
    transition: all .16s ease;
}

.vs-pay-option:hover {
    border-color: var(--line-strong);
    background: var(--surface-2);
}

.vs-pay-option:has(input:checked),
.vs-pay-option.is-active {
    border-color: var(--ink);
    background: color-mix(in srgb, var(--ink) 4%, var(--surface));
    box-shadow: 0 0 0 1px var(--ink);
}

.vs-pay-radio {
    accent-color: var(--ink);
    width: 17px;
    height: 17px;
    margin: 0;
    cursor: pointer;
}

.vs-pay-option__icon {
    width: 48px;
    height: 22px;
    object-fit: contain;
    border-radius: 4px;
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.06);
    flex-shrink: 0;
    padding: 2px 3px;
}

.vs-pay-option__name {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--ink);
}

/* Aside Panel: Ringkasan Pesanan */
aside.vs-panel {
    position: sticky;
    top: 88px;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 16px;
    box-shadow: var(--shadow-1);
    padding: 24px;
}

.vs-order__items {
    max-height: 280px;
    overflow-y: auto;
    margin-bottom: 16px;
    padding-right: 4px;
}

.vs-order__item {
    display: flex;
    gap: 12px;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid var(--line);
}

.vs-order__item:last-child {
    border-bottom: 0;
}

.vs-order__thumb {
    width: 46px;
    height: 46px;
    border-radius: 10px;
    background: var(--photo-tile);
    border: 1px solid var(--line);
    display: grid;
    place-items: center;
    padding: 3px;
    flex-shrink: 0;
    overflow: hidden;
}

.vs-order__thumb img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.vs-order__item b {
    font-size: 13px;
    font-weight: 600;
    color: var(--ink);
    display: block;
    line-height: 1.3;
}

.vs-order__item small {
    font-size: 11.5px;
    color: var(--ink-soft);
}

.vs-order__row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    font-size: 13px;
    color: var(--ink-soft);
}

.vs-order__row b {
    font-weight: 600;
    color: var(--ink);
}

.vs-order__total {
    border-top: 1.5px solid var(--line);
    margin-top: 12px;
    padding-top: 14px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    font-family: var(--font-display);
}

.vs-order__total span:first-child {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: var(--ink-mute);
}

.vs-order__total span:last-child {
    font-size: 21px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -0.5px;
}

/* Button System (Mengadopsi .ad-btn dari Admin) */
.vs-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-family: var(--font-display);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    border-radius: 10px;
    padding: 13px 20px;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s cubic-bezier(0.16, 1, 0.3, 1);
    box-sizing: border-box;
}

.vs-btn--block {
    width: 100%;
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

@media (max-width: 560px) {
    .vs-form-grid {
        grid-template-columns: 1fr;
    }
    .vs-panel {
        padding: 20px 16px;
    }
    .vs-pay-card__logos {
        max-width: 50%;
    }
    .vs-pay-badge {
        height: 18px;
        max-width: 38px;
    }
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    function syncPayStates() {
        const checked = document.querySelector('.vs-pay-radio:checked');
        document.querySelectorAll('.vs-pay-option').forEach(el => el.classList.remove('is-active'));
        document.querySelectorAll('.vs-pay-card').forEach(el => el.classList.remove('is-selected'));

        if (checked) {
            const opt = checked.closest('.vs-pay-option');
            if (opt) opt.classList.add('is-active');
            const card = checked.closest('.vs-pay-card');
            if (card) {
                card.classList.add('is-selected');
                if (card.tagName.toLowerCase() === 'details') {
                    card.open = true;
                }
            }
        }
    }

    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('vs-pay-radio')) {
            syncPayStates();
        }
    });

    document.addEventListener('DOMContentLoaded', syncPayStates);
    syncPayStates();
})();
@endpush
