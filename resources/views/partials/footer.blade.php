@php
    $wa = preg_replace('/\D+/', '', $storeSettings['chatbot_whatsapp'] ?? '');
@endphp

<footer class="vs-footer" style="margin-top: 60px; padding: 48px 0 28px; border-top: 1px solid var(--line); background: var(--surface);">
    <div class="vs-shell">
        <div class="vs-footer__grid" style="display: grid; grid-template-columns: 1.5fr 1fr 1fr 1.2fr; gap: 32px;">
            <div>
                <a href="{{ route('welcome') }}" class="ad-brand" style="margin-bottom: 12px;">
                    <span class="mark">
                        <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </span>
                    <span>
                        <b>LivingEase</b>
                        <small>FURNITURE &amp; LIVING</small>
                    </span>
                </a>
                <p style="font-size: 13px; color: var(--ink-soft); line-height: 1.6; margin-top: 8px;">
                    Perabot modern &amp; klasik pilihan dari kayu jati, kulit premium, dan besi tempa. Transaksi aman, pengiriman cepat ke seluruh Indonesia.
                </p>
            </div>

            <div>
                <h5 style="font-family: var(--font-display); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.6px; color: var(--ink-mute); margin-bottom: 12px;">
                    Katalog
                </h5>
                <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 8px; font-size: 13px;">
                    <li><a href="{{ route('home') }}" class="ad-link" style="padding: 4px 0; color: var(--ink-soft);">Semua produk</a></li>
                    @foreach ($navCategories->take(3) as $cat)
                        <li><a href="{{ route('home', ['cat_id' => $cat->id]) }}" class="ad-link" style="padding: 4px 0; color: var(--ink-soft);">{{ $cat->title }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h5 style="font-family: var(--font-display); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.6px; color: var(--ink-mute); margin-bottom: 12px;">
                    Akun &amp; Pesanan
                </h5>
                <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 8px; font-size: 13px;">
                    <li><a href="{{ route('cart.index') }}" class="ad-link" style="padding: 4px 0; color: var(--ink-soft);">Keranjang belanja</a></li>
                    <li><a href="{{ route('user.profile') }}" class="ad-link" style="padding: 4px 0; color: var(--ink-soft);">Pesanan saya</a></li>
                    <li><a href="{{ route('login') }}" class="ad-link" style="padding: 4px 0; color: var(--ink-soft);">Masuk / Daftar</a></li>
                </ul>
            </div>

            <div>
                <h5 style="font-family: var(--font-display); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.6px; color: var(--ink-mute); margin-bottom: 12px;">
                    Layanan Pelanggan
                </h5>
                <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 8px; font-size: 13px;">
                    @if (! empty($storeSettings['store_email']))
                        <li><a href="mailto:{{ $storeSettings['store_email'] }}" class="ad-link" style="padding: 4px 0; color: var(--ink-soft);">{{ $storeSettings['store_email'] }}</a></li>
                    @endif
                    @if ($wa)
                        <li><a href="https://wa.me/{{ $wa }}" rel="noopener" target="_blank" class="ad-link" style="padding: 4px 0; color: var(--ink-soft);">WhatsApp CS</a></li>
                    @endif
                    @if (! empty($storeSettings['store_phone']))
                        <li style="color: var(--ink-soft); font-size: 13px;">{{ $storeSettings['store_phone'] }}</li>
                    @endif
                </ul>
            </div>
        </div>

        <div style="margin-top: 36px; padding: 14px 20px; border-radius: 12px; background: var(--surface-2); border: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
            <span style="font-family: var(--font-display); font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--ink-soft);">
                Kanal Pembayaran Resmi:
            </span>
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <img src="{{ asset('img/pay/qris.svg') }}" alt="QRIS" style="height: 22px; padding: 2px 6px; background: #fff; border: 1px solid var(--line); border-radius: 6px;">
                <img src="{{ asset('img/pay/bca.svg') }}" alt="BCA" style="height: 22px; padding: 2px 6px; background: #fff; border: 1px solid var(--line); border-radius: 6px;">
                <img src="{{ asset('img/pay/mandiri.svg') }}" alt="Mandiri" style="height: 22px; padding: 2px 6px; background: #fff; border: 1px solid var(--line); border-radius: 6px;">
                <img src="{{ asset('img/pay/bni.svg') }}" alt="BNI" style="height: 22px; padding: 2px 6px; background: #fff; border: 1px solid var(--line); border-radius: 6px;">
                <img src="{{ asset('img/pay/bri.svg') }}" alt="BRI" style="height: 22px; padding: 2px 6px; background: #fff; border: 1px solid var(--line); border-radius: 6px;">
                <img src="{{ asset('img/pay/gopay.svg') }}" alt="GoPay" style="height: 22px; padding: 2px 6px; background: #fff; border: 1px solid var(--line); border-radius: 6px;">
                <img src="{{ asset('img/pay/shopeepay.svg') }}" alt="ShopeePay" style="height: 22px; padding: 2px 6px; background: #fff; border: 1px solid var(--line); border-radius: 6px;">
            </div>
        </div>

        <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; font-family: var(--font-display); font-size: 10px; letter-spacing: 1.2px; color: var(--ink-mute); text-transform: uppercase;">
            <span>&copy; {{ date('Y') }} {{ $storeSettings['store_name'] ?? 'LivingEase' }} · <a href="{{ route('admin.login') }}" style="color: var(--ink-mute); text-decoration: underline;">Admin Console</a></span>
            <span>{{ $storeSettings['store_address'] ?? 'Yogyakarta, Indonesia' }}</span>
        </div>
    </div>
</footer>
