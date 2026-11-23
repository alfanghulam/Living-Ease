<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <noscript><style>.vs-reveal,.vs-fu{opacity:1!important;transform:none!important}.vs-ln>span{transform:none!important}.vs-card .vs-card__tile{clip-path:none!important}.vs-story .w{opacity:1!important}</style></noscript>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($storeSettings['store_name'] ?? 'LivingEase').' — perabot klasik')</title>
    <meta name="description" content="@yield('meta_description', 'Perabot modern dan klasik dari material kayu, kulit, dan besi tempa pilihan. Belanja mudah, cepat, dan terpercaya di LivingEase.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Jost:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tema dipasang sebelum paint supaya tidak ada kedipan putih/hitam --}}
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

    <style>
    /* ================= TEMA MODERN PROFESIONAL (PUTIH & ABU-ABU) ================= */
    :root {
        --bg: #ffffff;
        --bg-soft: #f8fafc;
        --surface: #ffffff;
        --surface-2: #f1f5f9;
        --surface-3: #e2e8f0;
        --line: #e2e8f0;
        --line-strong: #cbd5e1;
        --header-bg: rgba(255, 255, 255, .92);
        --photo-tile: #f8fafc;

        --ink: #0f172a;
        --ink-soft: #475569;
        --ink-mute: #94a3b8;

        --accent: #0f172a;
        --on-accent: #ffffff;
        --accent-soft: #f1f5f9;
        --accent-line: #cbd5e1;
        --accent-2: #334155;
        --lime: #f1f5f9;
        --on-lime: #0f172a;
        --pink: #ef4444;
        --star: #f59e0b;
        --ok: #10b981;
        --warn: #f59e0b;
        --danger: #ef4444;

        --shadow-sm: 0 1px 3px rgba(0, 0, 0, .04), 0 1px 2px -1px rgba(0, 0, 0, .04);
        --shadow-1: 0 4px 6px -1px rgba(0, 0, 0, .05), 0 2px 4px -2px rgba(0, 0, 0, .05);
        --shadow-2: 0 12px 28px -6px rgba(15, 23, 42, .08), 0 8px 10px -6px rgba(15, 23, 42, .04);
        --shadow-accent: 0 4px 14px rgba(15, 23, 42, .14);

        --radius: 12px;
        --radius-sm: 8px;
        --radius-lg: 18px;
        --radius-full: 9999px;

        --font-sans: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
        --font-display: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;
        --font-serif: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;
    }

    :root[data-theme='dark'] {
        --bg: #0b0f19;
        --bg-soft: #111827;
        --surface: #111827;
        --surface-2: #1f2937;
        --surface-3: #374151;
        --line: #1f2937;
        --line-strong: #374151;
        --header-bg: rgba(11, 15, 25, .94);
        --photo-tile: #1a2234;

        --ink: #f9fafb;
        --ink-soft: #d1d5db;
        --ink-mute: #9ca3af;

        --accent: #f9fafb;
        --on-accent: #0b0f19;
        --accent-soft: #1f2937;
        --accent-line: #374151;
        --accent-2: #e5e7eb;
        --lime: #1f2937;
        --on-lime: #f9fafb;
        --pink: #f87171;
        --star: #fbbf24;
        --ok: #34d399;
        --warn: #fbbf24;
        --danger: #f87171;

        --shadow-sm: 0 1px 3px rgba(0, 0, 0, .3);
        --shadow-1: 0 4px 10px rgba(0, 0, 0, .4);
        --shadow-2: 0 16px 36px -10px rgba(0, 0, 0, .7);
        --shadow-accent: 0 2px 8px rgba(0, 0, 0, .5);
    }

    /* Hilangkan banner tulisan bergerak (marquee) sesuai permintaan user */
    .vs-marquee {
        display: none !important;
    }

    /* Base & Typography */
    body {
        background-color: var(--bg);
        color: var(--ink);
        font-family: var(--font-sans);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
        overflow-x: hidden;
    }

    h1, h2, h3, h4, h5, .vs-h1, .vs-logo__text {
        font-family: var(--font-display) !important;
        font-weight: 700;
        letter-spacing: -0.028em;
    }

    .vs-shell {
        width: 100%;
        max-width: 1240px;
        margin: 0 auto;
        padding: 0 24px;
    }

    /* Banner tulisan dihilangkan sesuai permintaan user */
    .vs-topbar {
        display: none !important;
    }

    /* Navbar Gaya Header Admin Panel */
    .vs-nav {
        position: sticky;
        top: 0;
        z-index: 60;
        background: var(--header-bg);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border-bottom: 1px solid var(--line);
        transition: background .2s ease, border-color .2s ease;
    }

    .vs-nav__inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 10px 0;
    }

    /* Brand Logo identik dengan .ad-brand Admin */
    .ad-brand {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        color: var(--ink);
    }

    .ad-brand .mark {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        background: var(--ink);
        color: var(--bg);
        flex-shrink: 0;
    }

    .ad-brand b {
        font-family: var(--font-display);
        font-size: 15px;
        letter-spacing: 1.6px;
        display: block;
        line-height: 1.1;
        color: var(--ink);
    }

    .ad-brand small {
        display: block;
        font-family: var(--font-display);
        font-size: 8px;
        letter-spacing: 2.2px;
        color: var(--accent-2);
        font-weight: 700;
        margin-top: 2px;
    }

    .vs-links {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .vs-links .ad-link {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 14px;
        border-radius: 8px;
        font-family: var(--font-display);
        font-size: 12.5px;
        font-weight: 600;
        color: var(--ink-soft);
        text-decoration: none;
        border: 1px solid transparent;
        transition: all .16s ease;
    }

    .vs-links .ad-link .vs-ico {
        width: 15px;
        height: 15px;
        opacity: .85;
    }

    .vs-links .ad-link:hover {
        color: var(--ink);
        background: var(--surface-2);
    }

    .vs-links .ad-link.is-active {
        color: var(--ink);
        background: var(--surface-2);
        border-color: var(--line-strong);
        font-weight: 700;
    }

    .vs-nav__tools {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .vs-btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: 1px solid var(--line);
        background: var(--surface);
        color: var(--ink);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .16s ease;
    }

    .vs-btn-icon:hover {
        background: var(--surface-2);
        color: var(--ink);
        border-color: var(--line-strong);
    }

    .ad-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 14px;
        border-radius: 9px;
        border: 1px solid var(--line);
        background: var(--surface);
        color: var(--ink);
        font-family: var(--font-display);
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all .16s ease;
    }

    .ad-btn:hover {
        background: var(--surface-2);
        border-color: var(--line-strong);
        color: var(--ink);
    }

    .vs-cart-btn {
        position: relative;
    }

    .vs-cart__count {
        display: inline-grid;
        place-items: center;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 6px;
        background: var(--ink);
        color: var(--bg);
        font-size: 10.5px;
        font-weight: 700;
        font-family: var(--font-display);
    }

    .ad-user {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-family: var(--font-display);
        font-size: 13px;
        font-weight: 700;
        background: var(--surface-2);
        color: var(--ink);
        border: 1px solid var(--line);
        text-decoration: none;
        transition: all .16s ease;
    }

    .ad-user:hover {
        background: var(--ink);
        color: var(--bg);
        border-color: var(--ink);
    }

    /* Product Grid & Cards (Gaya Admin Panel) */
    .vs-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .vs-card {
        display: flex;
        flex-direction: column;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 14px;
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        box-shadow: var(--shadow-1);
    }

    .vs-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-accent);
        border-color: var(--accent-line);
    }

    .vs-card__media {
        position: relative;
        display: block;
        background: var(--photo-tile);
        padding: 16px;
        overflow: hidden;
        border-bottom: 1px solid var(--line);
    }

    .vs-card__tile {
        display: flex;
        align-items: center;
        justify-content: center;
        aspect-ratio: 1 / 1;
        border-radius: 10px;
        overflow: hidden;
        background: transparent !important;
        clip-path: none !important;
    }

    .vs-card__tile img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        mix-blend-mode: multiply;
        transition: transform .25s ease;
        position: static !important;
        transform: none !important;
    }

    :root[data-theme='dark'] .vs-card__tile img {
        mix-blend-mode: normal;
    }

    .vs-card:hover .vs-card__tile img {
        transform: scale(1.04) !important;
    }

    .vs-card__peek {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translate(-50%, 6px);
        opacity: 0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 7px;
        background: var(--ink);
        color: var(--bg);
        font-family: var(--font-display);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .3px;
        box-shadow: var(--shadow-sm);
        pointer-events: none;
        transition: all 0.18s ease;
        white-space: nowrap;
        z-index: 3;
    }

    .vs-card:hover .vs-card__peek {
        opacity: 1;
        transform: translate(-50%, 0);
    }

    .vs-card__tags {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 2;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .vs-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 6px;
        font-family: var(--font-display);
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: .5px;
        background: var(--surface);
        color: var(--ink);
        border: 1px solid var(--line);
        box-shadow: var(--shadow-sm);
        text-transform: uppercase;
    }

    .vs-badge--sale {
        background: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
    }

    .vs-badge--360 {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }

    .vs-badge--out {
        background: #64748b;
        color: #ffffff;
        border-color: #64748b;
    }

    .vs-card__body {
        padding: 16px 18px 18px;
        display: flex;
        flex-direction: column;
        flex: 1;
        gap: 6px;
    }

    .vs-card__meta {
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: var(--ink-mute);
    }

    .vs-card__cat {
        color: #2563eb;
    }

    .vs-card__sep {
        color: var(--line-strong);
    }

    .vs-card__brand {
        color: var(--ink-soft);
    }

    .vs-card__title {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.35;
        color: var(--ink);
        font-family: var(--font-display) !important;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 40px;
    }

    .vs-card__title a {
        color: var(--ink) !important;
        text-decoration: none;
        transition: color .15s ease;
        background: none !important;
    }

    .vs-card__title a:hover {
        color: #2563eb !important;
    }

    .vs-card__rating {
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 4px 0 10px;
        font-size: 12px;
        color: var(--ink-mute);
    }

    .vs-card__rate-score {
        font-weight: 700;
        color: var(--ink);
    }

    .vs-card__rate-count, .vs-card__rate-empty {
        color: var(--ink-mute);
    }

    .vs-card__row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: auto;
        padding-top: 10px;
        border-top: 1px solid var(--line);
    }

    .vs-price {
        display: flex;
        flex-direction: column;
        line-height: 1.25;
    }

    .vs-price b, .vs-price__main {
        font-family: var(--font-display);
        font-size: 16.5px;
        font-weight: 800;
        color: var(--ink);
        letter-spacing: -0.01em;
    }

    .vs-price .vs-price-old {
        font-size: 11.5px;
        color: var(--ink-mute);
        text-decoration: line-through;
    }

    .vs-add {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 8px;
        background: var(--ink) !important;
        color: var(--bg) !important;
        border: 1px solid var(--ink) !important;
        font-family: var(--font-display);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all .16s ease;
        letter-spacing: normal !important;
        text-transform: none !important;
    }

    .vs-add:hover {
        background: var(--surface-2) !important;
        border-color: var(--ink) !important;
        color: var(--ink) !important;
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .vs-add:active {
        transform: scale(0.97);
    }

    .vs-add:disabled {
        opacity: .45;
        cursor: not-allowed;
        background: var(--surface-2) !important;
        border-color: var(--line) !important;
        color: var(--ink-mute) !important;
        box-shadow: none !important;
        transform: none !important;
    }

    /* Buttons (Gaya Admin .vs-btn / .ad-btn) */
    .vs-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 18px;
        border-radius: 10px;
        font-family: var(--font-display);
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: .4px;
        cursor: pointer;
        text-decoration: none;
        transition: all .16s ease;
        text-transform: none !important;
    }

    .vs-btn:active {
        transform: scale(0.98);
    }

    .vs-btn--primary, .vs-cta {
        background: var(--ink) !important;
        color: var(--bg) !important;
        border: 1px solid var(--ink) !important;
        box-shadow: var(--shadow-sm);
    }

    .vs-btn--primary:hover, .vs-cta:hover {
        background: var(--surface) !important;
        color: var(--ink) !important;
        border-color: var(--ink) !important;
        transform: translateY(-1px);
        box-shadow: var(--shadow-accent);
    }

    .vs-btn--line {
        background: var(--surface) !important;
        color: var(--ink) !important;
        border: 1px solid var(--line) !important;
    }

    .vs-btn--line:hover {
        background: var(--surface-2) !important;
        border-color: var(--line-strong) !important;
        transform: translateY(-1px);
    }

    .vs-btn--block {
        width: 100%;
    }

    /* Section Headings (Gaya .ad-head) */
    .vs-sechead {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .vs-sechead h2, .vs-h1 {
        font-family: var(--font-display) !important;
        font-size: clamp(22px, 2.6vw, 30px);
        font-weight: 700;
        letter-spacing: -0.8px;
        margin: 0;
        color: var(--ink);
    }

    .vs-sechead h2::after, .vs-h1::after {
        display: none !important;
    }

    .vs-sechead p {
        margin: 6px 0 0;
        color: var(--ink-soft);
        font-size: 12.5px;
    }

    .vs-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: var(--font-display);
        font-size: 12px;
        font-weight: 600;
        color: var(--ink) !important;
        text-decoration: none;
        padding: 7px 14px;
        border-radius: 9px;
        border: 1px solid var(--line);
        background: var(--surface);
        transition: all .16s ease;
    }

    .vs-link:hover {
        background: var(--surface-2) !important;
        border-color: var(--line-strong) !important;
    }

    /* Filters & Search */
    .vs-filters {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .vs-filter {
        padding: 7px 14px;
        border-radius: 8px;
        border: 1px solid var(--line);
        background: var(--surface);
        font-family: var(--font-display);
        font-size: 12px;
        font-weight: 600;
        color: var(--ink-soft) !important;
        text-decoration: none;
        transition: all .16s ease;
    }

    .vs-filter:hover {
        border-color: var(--line-strong) !important;
        color: var(--ink) !important;
        background: var(--surface-2) !important;
    }

    .vs-filter.is-on {
        background: var(--ink) !important;
        color: var(--bg) !important;
        border-color: var(--ink) !important;
        font-weight: 700;
        box-shadow: var(--shadow-sm);
    }

    .vs-searchbar {
        display: flex;
        gap: 10px;
        margin: 0 0 20px;
        max-width: 520px;
    }

    .vs-input, .vs-searchbar input {
        width: 100%;
        border: 1px solid var(--line);
        background: var(--surface);
        color: var(--ink);
        border-radius: 9px;
        padding: 9px 12px;
        font-family: var(--font-sans);
        font-size: 13px;
        outline: 0;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .vs-input:focus, .vs-searchbar input:focus {
        border-color: var(--ink);
        box-shadow: 0 0 0 2px var(--accent-soft);
    }

    /* Alerts & Notifications */
    .vs-alert {
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .vs-alert--ok {
        background: #ecfdf5;
        color: #065f46;
        border: 1.5px solid #a7f3d0;
    }
    .vs-alert--err, .vs-errors {
        background: #fef2f2;
        color: #991b1b;
        border: 1.5px solid #fecaca;
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    .vs-empty {
        text-align: center;
        padding: 60px 24px;
        background: var(--surface);
        border: 1.5px dashed var(--line);
        border-radius: 20px;
        grid-column: 1 / -1;
    }
    .vs-empty h3 {
        margin: 0 0 8px;
        font-size: 18px;
        font-weight: 700;
    }
    .vs-empty p {
        margin: 0 0 20px;
        color: var(--ink-soft);
        font-size: 14px;
    }

    /* Promo Box Banner */
    .vs-promo {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
        padding: 32px 36px;
        border-radius: 20px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff !important;
        box-shadow: 0 16px 36px -10px rgba(15, 23, 42, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 40px;
    }
    .vs-promo h2 {
        color: #ffffff !important;
        margin: 0 0 6px;
        font-size: clamp(20px, 2.4vw, 28px);
        font-weight: 800;
    }
    .vs-promo p {
        color: #cbd5e1 !important;
        margin: 0;
        font-size: 15px;
    }
    .vs-code {
        padding: 12px 24px;
        border-radius: 12px;
        border: 1.5px dashed rgba(255, 255, 255, 0.4);
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(8px);
        font-family: var(--font-display);
        font-size: 22px;
        font-weight: 800;
        letter-spacing: 0.08em;
        color: #ffffff;
    }

    /* Breadcrumbs */
    .vs-crumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--ink-mute);
        margin: 16px 0 24px;
    }
    .vs-crumbs a {
        color: var(--ink-soft);
        text-decoration: none;
        transition: color .15s ease;
    }
    .vs-crumbs a:hover {
        color: var(--ink);
    }
    .vs-crumbs span {
        color: var(--ink);
        font-weight: 600;
    }

    /* Product Detail Page (PDP) */
    .vs-pdp {
        display: grid;
        grid-template-columns: 1.15fr 1fr;
        gap: 40px;
        align-items: start;
        margin-bottom: 60px;
    }
    .vs-pdp__stage {
        background: var(--photo-tile);
        border: 1.5px solid var(--line);
        border-radius: 20px;
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 440px;
    }
    .vs-pdp__stage img {
        max-width: 90%;
        max-height: 420px;
        object-fit: contain;
    }
    .vs-pdp__info h1 {
        font-family: var(--font-display) !important;
        font-size: clamp(26px, 3.2vw, 36px);
        font-weight: 800;
        letter-spacing: -0.025em;
        margin: 0 0 12px;
        color: var(--ink);
    }
    .vs-pdp__price {
        display: flex;
        align-items: baseline;
        gap: 12px;
        margin: 18px 0;
    }
    .vs-pdp__price b {
        font-family: var(--font-display);
        font-size: 28px;
        font-weight: 800;
        color: var(--ink);
    }
    .vs-pdp__price s {
        font-size: 16px;
        color: var(--ink-mute);
    }
    .vs-pdp__price i {
        font-size: 12px;
        font-weight: 700;
        font-style: normal;
        padding: 3px 8px;
        background: #fee2e2;
        color: #dc2626;
        border-radius: 9999px;
    }
    .vs-seg {
        display: inline-flex;
        gap: 4px;
        padding: 4px;
        border-radius: 12px;
        background: var(--surface-2);
        border: 1px solid var(--line);
        margin-top: 12px;
    }
    .vs-seg button {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        border: 0;
        background: transparent;
        color: var(--ink-soft);
        cursor: pointer;
        transition: all .15s ease;
    }
    .vs-seg button[aria-selected="true"] {
        background: var(--surface);
        color: var(--ink);
        box-shadow: 0 1px 3px rgba(0, 0, 0, .08);
        font-weight: 700;
    }
    .vs-orbit {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 12px;
    }
    .vs-orbit button {
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid var(--line);
        background: var(--surface);
        color: var(--ink-soft);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s ease;
    }
    .vs-orbit button:hover {
        background: var(--ink);
        color: var(--bg);
        border-color: var(--ink);
    }

    /* Cart Modern Page (Gaya Admin Panel) */
    .vs-lines {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 28px;
    }
    .vs-line {
        display: grid;
        grid-template-columns: 80px 1fr auto;
        gap: 18px;
        align-items: center;
        padding: 16px 18px;
        border-radius: 14px;
        background: var(--surface);
        border: 1px solid var(--line);
        box-shadow: var(--shadow-1);
    }
    .vs-line__media {
        width: 80px;
        height: 80px;
        border-radius: 10px;
        background: var(--photo-tile);
        border: 1px solid var(--line);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .vs-line__media img {
        max-width: 80%;
        max-height: 80%;
        object-fit: contain;
    }
    .vs-line__title {
        margin: 0 0 4px;
        font-size: 15px;
        font-weight: 700;
        font-family: var(--font-display) !important;
    }
    .vs-line__title a {
        color: var(--ink);
        text-decoration: none;
    }
    .vs-line__title a:hover {
        color: var(--accent);
    }
    .vs-line__meta {
        margin: 0 0 10px;
        font-size: 12.5px;
        color: var(--ink-mute);
        font-family: var(--font-display);
    }
    .vs-line__controls {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .vs-qty {
        display: inline-flex;
        align-items: center;
        border: 1px solid var(--line);
        border-radius: 8px;
        overflow: hidden;
        background: var(--surface);
    }
    .vs-qty button {
        width: 30px;
        height: 30px;
        background: transparent;
        border: 0;
        color: var(--ink);
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .15s ease;
    }
    .vs-qty button:hover {
        background: var(--surface-2);
    }
    .vs-qty input {
        width: 40px;
        height: 30px;
        border: 0;
        text-align: center;
        font-size: 13px;
        font-weight: 700;
        background: transparent;
        color: var(--ink);
        outline: 0;
    }
    .vs-line__remove {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1px solid var(--line);
        background: var(--surface);
        color: var(--ink-mute);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .15s ease;
    }
    .vs-line__remove:hover {
        background: var(--surface-2);
        color: var(--danger);
        border-color: var(--line-strong);
    }
    .vs-line__subtotal {
        font-family: var(--font-display);
        font-size: 16px;
        font-weight: 700;
        color: var(--ink);
    }
    .vs-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 20px 24px;
        border-radius: 14px;
        background: var(--surface);
        border: 1px solid var(--line);
        box-shadow: var(--shadow-1);
    }
    .vs-summary__total {
        display: flex;
        flex-direction: column;
        font-family: var(--font-display);
        font-size: 22px;
        font-weight: 800;
        color: var(--ink);
    }
    .vs-summary__total small {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--ink-mute);
    }
    .vs-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Footer Modern */
    .vs-footer {
        margin-top: 80px;
        padding: 56px 0 32px;
        border-top: 1px solid var(--line);
        background: var(--bg-soft);
    }

    .vs-footer__grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr 1fr 1.2fr;
        gap: 36px;
    }

    .vs-footer__brand {
        font-family: var(--font-display) !important;
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 12px;
        color: var(--ink);
    }

    .vs-footer p {
        margin: 0;
        color: var(--ink-soft);
        font-size: 14px;
        line-height: 1.65;
        max-width: 36ch;
    }

    .vs-footer h5 {
        font-family: var(--font-display) !important;
        margin: 0 0 14px;
        font-size: 14px;
        font-weight: 700;
        color: var(--ink);
    }

    .vs-footer ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 10px;
        font-size: 14px;
        color: var(--ink-soft);
    }

    .vs-footer ul a {
        color: inherit;
        text-decoration: none;
        transition: color .15s ease;
    }

    .vs-footer ul a:hover {
        color: var(--ink);
        text-decoration: underline;
    }

    .vs-footer__payments {
        margin-top: 36px;
        padding: 18px 24px;
        border-radius: 14px;
        background: var(--surface);
        border: 1.5px solid var(--line);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .vs-footer__paylabel {
        font-family: var(--font-display) !important;
        font-size: 13px;
        font-weight: 700;
        color: var(--ink);
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .vs-footer__badges {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .vs-footer__badges .vs-pay-badge {
        height: 26px;
        padding: 2px 8px;
        background: #ffffff;
        border: 1px solid var(--line);
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        max-width: 60px;
    }

    /* Dialog Quick View Modern */
    .vs-qv {
        margin: auto;
        inset: 0;
        border: 1.5px solid var(--line);
        padding: 0;
        width: min(1040px, calc(100vw - 32px));
        max-height: calc(100svh - 40px);
        overflow: auto;
        background: var(--surface);
        color: var(--ink);
        border-radius: 24px;
        box-shadow: 0 30px 80px -20px rgba(0, 0, 0, 0.35);
    }

    .vs-qv::backdrop {
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .vs-qv__grid {
        display: grid;
        grid-template-columns: 1.15fr 1fr;
    }

    .vs-qv__stage {
        position: relative;
        background: var(--photo-tile);
        min-height: 480px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    .vs-qv__stage img {
        max-width: 86%;
        max-height: 86%;
        object-fit: contain;
    }

    .vs-qv__info {
        padding: 44px 36px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        gap: 12px;
    }

    .vs-qv__x {
        position: absolute;
        right: 18px;
        top: 18px;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 1px solid var(--line);
        background: var(--surface-2);
        color: var(--ink);
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.18s ease;
    }

    .vs-qv__x:hover {
        background: var(--ink);
        color: var(--bg);
        transform: rotate(90deg);
    }

    .vs-qv__cat {
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #2563eb;
        margin: 0;
    }

    .vs-qv__info h3 {
        font-size: clamp(24px, 2.5vw, 32px);
        font-weight: 800;
        line-height: 1.2;
        margin: 0;
        color: var(--ink);
    }

    .vs-qv__rate {
        font-size: 13.5px;
        color: var(--ink-mute);
        margin: 0;
    }

    .vs-qv__price b {
        font-size: 26px;
        font-weight: 800;
        color: var(--ink);
    }

    .vs-qv__desc {
        font-size: 14px;
        line-height: 1.65;
        color: var(--ink-soft);
        margin: 4px 0 12px;
    }

    .vs-footer__bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-top: 40px;
        padding-top: 22px;
        border-top: 1px solid var(--line);
        font-size: 13px;
        color: var(--ink-mute);
    }

    .vs-footer__bottom a {
        color: var(--ink-mute);
        text-decoration: underline;
    }

    .vs-footer__bottom a:hover {
        color: var(--ink);
    }

    @media (max-width: 1080px) {
        .vs-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .vs-footer__grid { grid-template-columns: 1fr 1fr; }
        .vs-links { display: none; }
        .vs-pdp { grid-template-columns: 1fr; gap: 24px; }
    }

    @media (max-width: 720px) {
        .vs-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .vs-footer__grid { grid-template-columns: minmax(0, 1fr); }
        .vs-burger { display: flex !important; }
        .vs-card__body { padding: 12px 14px 14px; }
        .vs-card__title { font-size: 13.5px; }
        .vs-qv__grid { grid-template-columns: 1fr; }
        .vs-qv__stage { min-height: 280px; }
        .vs-qv__info { padding: 24px 20px; }
        .vs-line { grid-template-columns: 70px 1fr; }
        .vs-line__subtotal { grid-column: 1 / -1; text-align: right; }
    }
    </style>
    @stack('styles')
</head>
<body>
    <div class="vs-prog" id="vs-prog" aria-hidden="true"></div>
    <a class="vs-skip" href="#konten">Lewati ke konten</a>

    @include('partials.navbar')

    <main id="konten" class="vs-main">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.quickview')
    @include('partials.chat')

    <div class="vs-toast" id="vs-toast" role="status" aria-live="polite">
        <span class="vs-toast__icon">
            <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
        </span>
        <span id="vs-toast-text">Masuk keranjang</span>
    </div>

    @stack('scripts')
</body>
</html>
