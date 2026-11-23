<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — LivingEase</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600&display=swap" rel="stylesheet">

    <script>
        (function () {
            try {
                var t = localStorage.getItem('vanz-theme');
                if (t !== 'dark' && t !== 'light') {
                    t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                document.documentElement.dataset.theme = t;
                document.documentElement.style.colorScheme = t;
            } catch (e) {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])
</head>
<body>
    <div class="ad-app">
        <aside class="ad-side" data-side>
            <a href="{{ route('admin.dashboard') }}" class="ad-brand">
                <span class="mark">
                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </span>
                <span>
                    <b>LivingEase</b>
                    <small>ADMIN CONSOLE</small>
                </span>
            </a>

            <nav class="ad-nav" aria-label="Navigasi admin">
                <div class="ad-navgroup">
                    <div class="ad-navgroup__label">Overview</div>
                    <a href="{{ route('admin.dashboard') }}" @class(['ad-link', 'is-active' => request()->routeIs('admin.dashboard')])>
                        <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>
                        Dashboard
                    </a>
                </div>

                <div class="ad-navgroup">
                    <div class="ad-navgroup__label">Katalog</div>
                    <a href="{{ route('admin.products.index') }}" @class(['ad-link', 'is-active' => request()->routeIs('admin.products.*')])>
                        <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7l8-4 8 4v10l-8 4-8-4z"/><path d="M4 7l8 4 8-4M12 11v10"/></svg>
                        Produk
                    </a>
                    <a href="{{ route('admin.categories.index') }}" @class(['ad-link', 'is-active' => request()->routeIs('admin.categories.*')])>
                        <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"/></svg>
                        Kategori
                    </a>
                    <a href="{{ route('admin.brands.index') }}" @class(['ad-link', 'is-active' => request()->routeIs('admin.brands.*')])>
                        <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12l4-4 5 3 5-3 4 4-9 8z"/></svg>
                        Brand
                    </a>
                </div>

                <div class="ad-navgroup">
                    <div class="ad-navgroup__label">Penjualan</div>
                    <a href="{{ route('admin.orders.index') }}" @class(['ad-link', 'is-active' => request()->routeIs('admin.orders.*')])>
                        <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2z"/><path d="M9 8h6M9 12h6"/></svg>
                        Pesanan
                    </a>
                    <a href="{{ route('admin.customers.index') }}" @class(['ad-link', 'is-active' => request()->routeIs('admin.customers.*')])>
                        <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3.4"/><path d="M3 20a6 6 0 0 1 12 0"/><path d="M16 5.5a3 3 0 0 1 0 5.6M18 20a5.5 5.5 0 0 0-3-4.9"/></svg>
                        Pelanggan
                    </a>
                </div>

                <div class="ad-navgroup">
                    <div class="ad-navgroup__label">Komunikasi</div>
                    <a href="{{ route('admin.chat.index') }}" @class(['ad-link', 'is-active' => request()->routeIs('admin.chat.*')])>
                        <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a8 8 0 0 1-8 8H8l-5 3 1.4-5.2A8 8 0 1 1 21 12z"/></svg>
                        Chat
                    </a>
                </div>

                <div class="ad-navgroup">
                    <div class="ad-navgroup__label">Sistem</div>
                    <a href="{{ route('admin.settings.index') }}" @class(['ad-link', 'is-active' => request()->routeIs('admin.settings.*')])>
                        <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>
                        Pengaturan
                    </a>
                </div>
            </nav>

            <div class="ad-side__foot">
                <a href="{{ route('home') }}" class="ad-link">
                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 4h6v6"/><path d="M20 4l-9 9"/><path d="M10 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-4"/></svg>
                    Lihat toko
                </a>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="ad-link ad-link--danger">
                        <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <button type="button" class="ad-scrim" data-side-toggle aria-label="Tutup menu"></button>

        <div class="ad-shell">
            <header class="ad-top">
                <button type="button" class="vs-btn-icon ad-burger" data-side-toggle aria-label="Buka menu">
                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>

                <span class="ad-crumb">@yield('crumb', 'Admin')</span>

                <div class="ad-tools">
                    <button type="button" class="vs-btn-icon vs-theme" data-theme-toggle aria-label="Ganti tema" title="Ganti tema">
                        <svg class="vs-ico vs-sun" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="4"/>
                            <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>
                        </svg>
                        <svg class="vs-ico vs-moon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M21 12.8A8.5 8.5 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/>
                        </svg>
                    </button>

                    <span class="ad-user" title="{{ auth()->user()?->name }}">
                        {{ Str::upper(Str::substr(auth()->user()?->name ?? 'A', 0, 1)) }}
                    </span>
                </div>
            </header>

            <main class="ad-main">
                @if (session('success'))
                    <div class="vs-alert vs-alert--ok ad-flash">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="vs-alert vs-alert--err ad-flash">{{ session('error') }}</div>
                @endif

                @yield('content')
            </main>

            <footer class="ad-foot">
                &copy; {{ date('Y') }} VANZSTORE ADMIN · {{ Str::upper(auth()->user()?->email ?? '') }}
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
