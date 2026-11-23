@php $storeName = $storeSettings['store_name'] ?? 'LivingEase'; @endphp

<header class="vs-nav" data-nav>
    <div class="vs-shell">
        <div class="vs-nav__inner">
            <a href="{{ route('welcome') }}" class="ad-brand" aria-label="{{ $storeName }}, beranda">
                <span class="mark">
                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </span>
                <span>
                    <b>LivingEase</b>
                    <small>OFFICIAL STORE</small>
                </span>
            </a>

            <nav class="vs-links" aria-label="Navigasi utama">
                <a href="{{ route('welcome') }}" @class(['ad-link', 'is-active' => request()->routeIs('welcome')])>
                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Beranda
                </a>
                <a href="{{ route('home') }}" @class(['ad-link', 'is-active' => request()->routeIs('home', 'products.*')])>
                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7l8-4 8 4v10l-8 4-8-4z"/><path d="M4 7l8 4 8-4M12 11v10"/></svg>
                    Koleksi
                </a>
                @if (($storeSettings['promo_active'] ?? '0') === '1')
                    <a href="{{ route('home') }}#promo" class="ad-link">
                        <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        Promo
                    </a>
                @endif
            </nav>

            <div class="vs-nav__tools">
                <a href="{{ route('home') }}#cari" class="vs-btn-icon" aria-label="Cari produk" title="Cari produk">
                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/></svg>
                </a>

                <button type="button" class="vs-btn-icon vs-theme" data-theme-toggle aria-label="Ganti tema terang atau gelap" title="Ganti tema">
                    <svg class="vs-ico vs-sun" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="4"/>
                        <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>
                    </svg>
                    <svg class="vs-ico vs-moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12.8A8.5 8.5 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
                </button>

                <a href="{{ route('cart.index') }}" class="ad-btn vs-cart-btn" aria-label="Keranjang belanja">
                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    <span class="vs-cart__label">Keranjang</span>
                    <span class="vs-cart__count" data-cart-count>{{ $cartCount ?? 0 }}</span>
                </a>

                @auth
                    <a href="{{ route('user.profile') }}" class="ad-user" title="{{ auth()->user()->name }}, profil saya">
                        {{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="vs-btn vs-btn--primary" style="padding: 7px 14px; font-size: 12px; border-radius: 8px;">Masuk</a>
                @endauth

                <button type="button" class="vs-btn-icon vs-burger" data-drawer-toggle aria-label="Buka menu" aria-expanded="false">
                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div class="vs-drawer" data-drawer>
        <div class="vs-shell">
            <a href="{{ route('welcome') }}" class="ad-link">Beranda</a>
            <a href="{{ route('home') }}" class="ad-link">Koleksi</a>
            @if (($storeSettings['promo_active'] ?? '0') === '1')
                <a href="{{ route('home') }}#promo" class="ad-link">Promo</a>
            @endif
            @auth
                <a href="{{ route('user.profile') }}" class="ad-link">Profil saya</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="ad-link ad-link--danger" style="margin-top: 8px;">Keluar</button></form>
            @else
                <a href="{{ route('login') }}" class="ad-link">Masuk</a>
                <a href="{{ route('register') }}" class="ad-link">Daftar</a>
            @endauth
        </div>
    </div>
</header>
