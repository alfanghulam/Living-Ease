@extends('layouts.app')

@section('title', ($storeSettings['store_name'] ?? 'LivingEase').' — perabot klasik')
@section('meta_description', 'Perabot modern dan klasik dari material kayu, kulit, dan besi tempa pilihan. Belanja mudah, cepat, dan terpercaya di LivingEase.')

@section('content')
    <div class="vs-shell" style="padding-top: 24px;">
        {{-- HEAD SECTION GAYA ADMIN --}}
        <div class="ad-head">
            <div>
                <span class="ad-crumb" style="display:inline-block; margin-bottom: 6px;">OFFICIAL STORE · KOLEKSI TERBARU</span>
                <h1>LivingEase <em>Furniture</em></h1>
                <p>Koleksi perabot modern & klasik dari material kayu jati pilihan, kulit premium, dan besi tempa.</p>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <a href="{{ route('home') }}" class="vs-btn vs-btn--primary">Jelajahi Katalog →</a>
                @if (($storeSettings['promo_active'] ?? '0') === '1')
                    <a href="{{ route('home') }}#promo" class="ad-btn">Katalog Promo</a>
                @endif
            </div>
        </div>

        {{-- SHOWCASE GRID GAYA .ad-grid2 --}}
        <div class="ad-grid2" style="margin-bottom: 24px; align-items: stretch;">
            {{-- Card Kiri: Promo Banner --}}
            <div class="ad-panel" style="display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; padding: 24px; height: 100%; box-sizing: border-box;">
                <div>
                    <span class="ad-auth-badge" style="margin-bottom: 12px;">
                        <span class="vs-dot"></span>
                        PROMO SPESIAL 2026
                    </span>
                    <h2 style="font-family: var(--font-display); font-size: clamp(20px, 2.2vw, 24px); letter-spacing: -0.6px; margin: 0 0 8px; line-height: 1.25;">
                        Hemat Hingga 40% &amp; Bebas Ongkir
                    </h2>
                    <p style="font-size: 13px; color: var(--ink-soft); line-height: 1.6; margin: 0 0 16px;">
                        Gunakan kode voucher <b style="color: var(--ink);">GERCEP40</b> saat checkout untuk potongan harga langsung ke seluruh kota di Indonesia.
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--line);">
                    <a href="{{ route('home') }}" class="vs-btn vs-btn--primary" style="padding: 7px 16px; font-size: 13px;">Belanja Sekarang</a>
                    <a href="#koleksi" class="ad-btn" style="padding: 7px 16px; font-size: 13px;">Lihat Koleksi</a>
                </div>
            </div>

            {{-- Card Kanan: Showcase Slider Otomatis --}}
            <div class="ad-panel" id="ad-slider-box" style="position: relative; overflow: hidden; background: var(--surface-2); padding: 24px; display: flex; flex-direction: column; justify-content: space-between; height: 100%; box-sizing: border-box;">
                {{-- Viewport Terisolasi (Overflow Hidden) Supaya Slide Tetangga Tidak Bocor di Padding --}}
                <div style="overflow: hidden; width: 100%; position: relative; flex: 1; display: flex; align-items: center;">
                    <div id="ad-slider-track" style="display: flex; width: 100%; transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1); will-change: transform;">
                        {{-- Slide 1: 3D Model --}}
                        <div class="ad-slide-item" style="flex: 0 0 100%; min-width: 100%; width: 100%; box-sizing: border-box; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                            <div style="flex: 1; min-width: 0;">
                                <span class="ad-crumb" style="color: var(--accent); font-weight: 700; margin-bottom: 6px; display: block; font-size: 11px; letter-spacing: 0.5px;">INSPEKSI PRODUK REALISTIS</span>
                                <h3 style="font-family: var(--font-display); font-size: clamp(16px, 1.8vw, 19px); font-weight: 700; margin: 0 0 6px; color: var(--ink); line-height: 1.3;">
                                    Putar Model 3D 360°
                                </h3>
                                <p style="font-size: 12.5px; color: var(--ink-soft); line-height: 1.5; margin: 0 0 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    Inspeksi detail serat kayu, lekukan sofa, dan dimensi ruang langsung di browsermu sebelum memesan.
                                </p>
                                <a href="{{ route('home') }}" class="ad-btn" style="padding: 7px 14px; font-size: 12px;">
                                    <span>Coba Fitur 3D</span>
                                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true" style="width: 13px; height: 13px;"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </a>
                            </div>
                            <div style="width: 110px; height: 110px; border-radius: 12px; background: var(--surface); border: 1px solid var(--line); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; box-shadow: var(--shadow-sm);">
                                <img src="{{ asset('img/products/sofa-chesterfield-kulit.webp') }}" alt="Model 3D" style="max-width: 88%; max-height: 88%; object-fit: contain;">
                            </div>
                        </div>

                        {{-- Slide 2: Set Meja Makan --}}
                        <div class="ad-slide-item" style="flex: 0 0 100%; min-width: 100%; width: 100%; box-sizing: border-box; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                            <div style="flex: 1; min-width: 0;">
                                <span class="ad-crumb" style="color: var(--accent); font-weight: 700; margin-bottom: 6px; display: block; font-size: 11px; letter-spacing: 0.5px;">FURNITUR RUANG MAKAN</span>
                                <h3 style="font-family: var(--font-display); font-size: clamp(16px, 1.8vw, 19px); font-weight: 700; margin: 0 0 6px; color: var(--ink); line-height: 1.3;">
                                    Set Meja Makan Bundar
                                </h3>
                                <p style="font-size: 12.5px; color: var(--ink-soft); line-height: 1.5; margin: 0 0 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    Material kayu jati solid dengan sentuhan finishing halus, menciptakan kehangatan berkumpul keluarga.
                                </p>
                                <a href="{{ route('home') }}" class="ad-btn" style="padding: 7px 14px; font-size: 12px;">
                                    <span>Lihat Produk</span>
                                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true" style="width: 13px; height: 13px;"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </a>
                            </div>
                            <div style="width: 110px; height: 110px; border-radius: 12px; background: var(--surface); border: 1px solid var(--line); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; box-shadow: var(--shadow-sm);">
                                <img src="{{ asset('img/products/set-meja-makan-bundar.webp') }}" alt="Set Meja Makan" style="max-width: 88%; max-height: 88%; object-fit: contain;">
                            </div>
                        </div>

                        {{-- Slide 3: Meja Samping Mahoni --}}
                        <div class="ad-slide-item" style="flex: 0 0 100%; min-width: 100%; width: 100%; box-sizing: border-box; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                            <div style="flex: 1; min-width: 0;">
                                <span class="ad-crumb" style="color: var(--accent); font-weight: 700; margin-bottom: 6px; display: block; font-size: 11px; letter-spacing: 0.5px;">KLASIK ELEGAN</span>
                                <h3 style="font-family: var(--font-display); font-size: clamp(16px, 1.8vw, 19px); font-weight: 700; margin: 0 0 6px; color: var(--ink); line-height: 1.3;">
                                    Meja Samping Mahoni
                                </h3>
                                <p style="font-size: 12.5px; color: var(--ink-soft); line-height: 1.5; margin: 0 0 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    Ukiran rapi dan kokoh khas pengrajin berpengalaman untuk mempercantik sudut kamar dan ruang tamu.
                                </p>
                                <a href="{{ route('home') }}" class="ad-btn" style="padding: 7px 14px; font-size: 12px;">
                                    <span>Lihat Koleksi</span>
                                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true" style="width: 13px; height: 13px;"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </a>
                            </div>
                            <div style="width: 110px; height: 110px; border-radius: 12px; background: var(--surface); border: 1px solid var(--line); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; box-shadow: var(--shadow-sm);">
                                <img src="{{ asset('img/products/meja-samping-mahoni.webp') }}" alt="Meja Samping" style="max-width: 88%; max-height: 88%; object-fit: contain;">
                            </div>
                        </div>

                        {{-- Slide 4: Lampu Dinding Lentera --}}
                        <div class="ad-slide-item" style="flex: 0 0 100%; min-width: 100%; width: 100%; box-sizing: border-box; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                            <div style="flex: 1; min-width: 0;">
                                <span class="ad-crumb" style="color: var(--accent); font-weight: 700; margin-bottom: 6px; display: block; font-size: 11px; letter-spacing: 0.5px;">PENCAHAYAAN RUANG</span>
                                <h3 style="font-family: var(--font-display); font-size: clamp(16px, 1.8vw, 19px); font-weight: 700; margin: 0 0 6px; color: var(--ink); line-height: 1.3;">
                                    Lampu Dinding Lentera
                                </h3>
                                <p style="font-size: 12.5px; color: var(--ink-soft); line-height: 1.5; margin: 0 0 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    Rangka besi tempa anti-karat dengan cahaya hangat untuk menciptakan suasana malam yang menenangkan.
                                </p>
                                <a href="{{ route('home') }}" class="ad-btn" style="padding: 7px 14px; font-size: 12px;">
                                    <span>Lihat Produk</span>
                                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true" style="width: 13px; height: 13px;"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </a>
                            </div>
                            <div style="width: 110px; height: 110px; border-radius: 12px; background: var(--surface); border: 1px solid var(--line); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; box-shadow: var(--shadow-sm);">
                                <img src="{{ asset('img/products/lampu-dinding-lentera.webp') }}" alt="Lampu Dinding" style="max-width: 88%; max-height: 88%; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Indikator & Tombol Kontrol Slider --}}
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--line);">
                    <div class="ad-slide-dots" id="ad-slide-dots" style="display: flex; gap: 6px; align-items: center;">
                        <button type="button" class="ad-dot is-active" data-idx="0" aria-label="Slide 1"></button>
                        <button type="button" class="ad-dot" data-idx="1" aria-label="Slide 2"></button>
                        <button type="button" class="ad-dot" data-idx="2" aria-label="Slide 3"></button>
                        <button type="button" class="ad-dot" data-idx="3" aria-label="Slide 4"></button>
                    </div>
                    <div style="display: flex; gap: 6px;">
                        <button type="button" id="ad-btn-prev" class="vs-btn-icon" style="width: 28px; height: 28px; border-radius: 6px;" aria-label="Sebelumnya">
                            <svg class="vs-ico" viewBox="0 0 24 24" style="width: 13px; height: 13px;"><path d="m15 18-6-6 6-6"/></svg>
                        </button>
                        <button type="button" id="ad-btn-next" class="vs-btn-icon" style="width: 28px; height: 28px; border-radius: 6px;" aria-label="Selanjutnya">
                            <svg class="vs-ico" viewBox="0 0 24 24" style="width: 13px; height: 13px;"><path d="m9 6 6 6-6 6"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- KEUNGGULAN TOKO DENGAN GAYA .ad-stats ADMIN PANEL --}}
        <div class="ad-stats" style="margin-bottom: 36px;">
            <div class="ad-stat">
                <span class="ad-stat__icon">
                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </span>
                <div class="ad-stat__label">PENGIRIMAN NASIONAL</div>
                <div class="ad-stat__value" style="font-size: 17px; font-weight: 700; margin-top: 6px;">Bebas Ongkir</div>
                <div style="font-size: 11.5px; color: var(--ink-soft); margin-top: 4px;">Ke seluruh kota di Indonesia</div>
            </div>

            <div class="ad-stat">
                <span class="ad-stat__icon">
                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
                </span>
                <div class="ad-stat__label">VISUALISASI 360°</div>
                <div class="ad-stat__value" style="font-size: 17px; font-weight: 700; margin-top: 6px;">Model 3D</div>
                <div style="font-size: 11.5px; color: var(--ink-soft); margin-top: 4px;">Putar dan amati secara nyata</div>
            </div>

            <div class="ad-stat">
                <span class="ad-stat__icon">
                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </span>
                <div class="ad-stat__label">TRANSAKSI INSTAN</div>
                <div class="ad-stat__value" style="font-size: 17px; font-weight: 700; margin-top: 6px;">Pembayaran Aman</div>
                <div style="font-size: 11.5px; color: var(--ink-soft); margin-top: 4px;">QRIS, Virtual Account, & E-Wallet</div>
            </div>

            <div class="ad-stat">
                <span class="ad-stat__icon">
                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </span>
                <div class="ad-stat__label">TESTIMONI ASLI</div>
                <div class="ad-stat__value" style="font-size: 17px; font-weight: 700; margin-top: 6px;">Ulasan Terverifikasi</div>
                <div style="font-size: 11.5px; color: var(--ink-soft); margin-top: 4px;">Rating asli pembeli sebenarnya</div>
            </div>
        </div>

        {{-- KOLEKSI UNGGULAN --}}
        <section class="vs-section" id="koleksi">
            <div class="vs-sechead vs-reveal">
                <div>
                    <h2>Koleksi Pilihan</h2>
                    <p>Perabot terfavorit untuk ruang keluarga, ruang kerja, dan ruang makan Anda.</p>
                </div>
                <a class="vs-link" href="{{ route('home') }}">
                    <span>Semua produk</span>
                    <svg class="vs-ico vs-ico--sm" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>

            <div class="vs-grid">
                @foreach ($featured as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>

        {{-- KATEGORI --}}
        @if ($categories->isNotEmpty())
            <section class="vs-section" id="kategori">
                <div class="vs-sechead vs-reveal">
                    <div>
                        <h2>Jelajahi Menurut Kategori</h2>
                        <p>Pilih kategori yang tepat untuk melengkapi sudut hunian Anda.</p>
                    </div>
                </div>

                <div class="vs-catlist">
                    @foreach ($categories as $category)
                        @php
                            $catLower = strtolower($category->title);
                            $icon = 'furnitur';
                            if (str_contains($catLower, 'sofa') || str_contains($catLower, 'tamu')) {
                                $icon = 'sofa';
                            } elseif (str_contains($catLower, 'kursi') || str_contains($catLower, 'kerja')) {
                                $icon = 'chair';
                            } elseif (str_contains($catLower, 'meja') || str_contains($catLower, 'makan')) {
                                $icon = 'table';
                            } elseif (str_contains($catLower, 'tidur') || str_contains($catLower, 'kamar')) {
                                $icon = 'bed';
                            } elseif (str_contains($catLower, 'dekor') || str_contains($catLower, 'lampu')) {
                                $icon = 'decor';
                            }
                        @endphp
                        <a class="vs-catrow vs-reveal" href="{{ route('home', ['cat_id' => $category->id]) }}">
                            <div class="vs-catrow__icon">
                                @if ($icon === 'sofa')
                                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 11V7a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v4"/><path d="M2 13a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2z"/><path d="M6 18v2M18 18v2"/></svg>
                                @elseif ($icon === 'chair')
                                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 13h10M7 13V6a3 3 0 0 1 6 0v7M7 13v7M17 13v7"/></svg>
                                @elseif ($icon === 'table')
                                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 8h18v3H3zM5 11v9M19 11v9M9 11v5M15 11v5"/></svg>
                                @elseif ($icon === 'bed')
                                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 4v16M2 8h18a2 2 0 0 1 2 2v10M2 17h20M6 8v4"/></svg>
                                @elseif ($icon === 'decor')
                                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 21h6M12 3a6 6 0 0 0-6 6c0 2.2 1.3 4.1 3 5v3h6v-3c1.7-.9 3-2.8 3-5a6 6 0 0 0-6-6z"/></svg>
                                @else
                                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 9h18M9 21V9"/></svg>
                                @endif
                            </div>
                            <span class="vs-catrow__name">{{ $category->title }}</span>
                            <span class="vs-catrow__count">{{ $categoryCounts[$category->id] ?? 0 }} produk</span>
                            <span class="vs-catrow__arrow">
                                <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    {{-- BAND HIGHLIGHT PARALLAX --}}
    <section class="vs-band">
        <img class="vs-band__img" src="{{ asset('img/hero-room.jpg') }}" alt="" aria-hidden="true" loading="lazy">
        <div class="vs-band__shade"></div>
        <div class="vs-band__copy vs-reveal">
            <span class="vs-band__kicker">✨ KUALITAS TERBAIK</span>
            <h2>Hadirkan Kehangatan & Estetika di Rumah Anda</h2>
            <p>Setiap perabot dirancang dengan ketelitian, material premium, dan kenyamanan maksimal.</p>
            <a class="vs-cta" href="{{ route('home') }}">
                <span>Jelajahi Koleksi</span>
                <svg class="vs-ico vs-ico--sm" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </div>
    </section>

    <div class="vs-shell">
        {{-- KENAPA LIVINGEASE --}}
        <section class="vs-section" id="kenapa">
            <div class="vs-sechead vs-reveal">
                <div>
                    <h2>Kenapa Memilih LivingEase</h2>
                    <p>Standar kualitas terbaik dan pengalaman belanja perabot modern yang mudah.</p>
                </div>
            </div>
            <div class="vs-values">
                <div class="vs-val vs-reveal">
                    <div class="vs-val__top">
                        <span class="vs-val__num">01</span>
                        <div class="vs-val__icon">
                            <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        </div>
                    </div>
                    <h3>Putar dari Segala Sisi</h3>
                    <p>Produk yang sudah memiliki model 3D dapat diputar ke segala arah dan diperiksa detailnya secara realistis sebelum Anda membeli.</p>
                </div>
                <div class="vs-val vs-reveal">
                    <div class="vs-val__top">
                        <span class="vs-val__num">02</span>
                        <div class="vs-val__icon">
                            <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 11 12 14 22 4"/></svg>
                        </div>
                    </div>
                    <h3>Ulasan Terpercaya</h3>
                    <p>Hanya pelanggan dengan pesanan berstatus selesai yang dapat memberikan rating bintang serta ulasan terverifikasi.</p>
                </div>
                <div class="vs-val vs-reveal">
                    <div class="vs-val__top">
                        <span class="vs-val__num">03</span>
                        <div class="vs-val__icon">
                            <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        </div>
                    </div>
                    @if (app(\App\Services\MidtransService::class)->isConfigured())
                        <h3>Pembayaran Instan & Aman</h3>
                        <p>Mendukung QRIS, transfer bank Virtual Account (BCA, Mandiri, BRI, BNI), dan e-wallet dengan verifikasi otomatis.</p>
                    @else
                        <h3>Pesanan Dipantau Admin</h3>
                        <p>Setelah pesanan dibuat, tim admin kami mengonfirmasi detail pembayaran dan pengiriman secara personal.</p>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
<style>
/* ================= E-COMMERCE BANNER CAROUSEL DENGAN SLIDE HORISONTAL NYATA (GAYA ADMIN PANEL) ================= */
.vs-carousel-wrapper {
    padding-top: 24px;
    padding-bottom: 8px;
}

.vs-carousel {
    position: relative;
    width: 100%;
    overflow: hidden;
    background: #0f172a;
    border-radius: 18px;
    height: clamp(380px, 48vh, 480px);
    border: 1px solid var(--line);
    box-shadow: var(--shadow-2);
}

.vs-carousel__track {
    display: flex;
    width: 100%;
    height: 100%;
    transition: transform 0.65s cubic-bezier(0.25, 1, 0.5, 1);
    will-change: transform;
}

.vs-slide {
    flex: 0 0 100%;
    width: 100%;
    min-width: 100%;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.vs-slide__bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    transform: scale(1.02);
    transition: transform 6s cubic-bezier(.25, .46, .45, .94);
}

.vs-slide.is-active .vs-slide__bg {
    transform: scale(1.08);
}

.vs-slide__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(15, 23, 42, 0.92) 0%, rgba(15, 23, 42, 0.70) 50%, rgba(15, 23, 42, 0.35) 100%);
}

.vs-slide__overlay--dark {
    background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.76) 50%, rgba(15, 23, 42, 0.4) 100%);
}

.vs-slide__overlay--slate {
    background: linear-gradient(90deg, rgba(15, 23, 42, 0.94) 0%, rgba(30, 41, 59, 0.78) 50%, rgba(15, 23, 42, 0.35) 100%);
}

.vs-slide__content {
    position: relative;
    z-index: 3;
    height: 100%;
    display: flex;
    align-items: center;
    padding: 0 clamp(24px, 5vw, 64px);
}

.vs-slide__glass {
    max-width: 580px;
    color: #ffffff;
    padding: 32px 36px;
    border-radius: 18px;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.14);
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.4);
}

.vs-slide__pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 4px 12px;
    border-radius: 6px;
    font-family: var(--font-display);
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 1.4px;
    text-transform: uppercase;
    background: rgba(255, 255, 255, 0.14);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff;
    margin-bottom: 16px;
}

.vs-slide__pill--promo {
    background: rgba(239, 68, 68, 0.22);
    border-color: rgba(239, 68, 68, 0.5);
    color: #fecaca;
}

.vs-slide__pill--tech {
    background: rgba(59, 130, 246, 0.22);
    border-color: rgba(59, 130, 246, 0.5);
    color: #bfdbfe;
}

.vs-pulse {
    display: inline-block;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: vs-pulse-anim 2s infinite;
}

.vs-pulse--red {
    background: #ef4444;
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
}

.vs-pulse--blue {
    background: #3b82f6;
    box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
}

@keyframes vs-pulse-anim {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(255, 255, 255, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
}

.vs-slide__title {
    font-family: var(--font-display) !important;
    font-size: clamp(25px, 3.2vw, 38px);
    font-weight: 800;
    line-height: 1.18;
    letter-spacing: -0.025em;
    color: #ffffff !important;
    margin: 0 0 14px;
}

.vs-slide__desc {
    font-size: clamp(13.5px, 1.2vw, 15px);
    line-height: 1.6;
    color: rgba(248, 250, 252, 0.9) !important;
    margin: 0 0 22px;
    max-width: 500px;
}

.vs-slide__actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

/* Tombol Banner (Gaya Tombol Admin Panel .ad-btn) */
.vs-slide__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 22px;
    border-radius: 10px;
    font-family: var(--font-display);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
    text-decoration: none;
    box-sizing: border-box;
}

.vs-slide__btn--primary {
    background: #ffffff !important;
    color: #0f172a !important;
    border: 1px solid #ffffff !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
}

.vs-slide__btn--primary:hover {
    background: #0f172a !important;
    color: #ffffff !important;
    border-color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}

.vs-slide__btn--ghost {
    background: rgba(255, 255, 255, 0.12) !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

.vs-slide__btn--ghost:hover {
    background: #ffffff !important;
    color: #0f172a !important;
    border-color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
}

/* Panah Navigasi */
.vs-carousel__arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.vs-carousel__arrow:hover {
    background: #ffffff;
    color: #0f172a;
    border-color: #ffffff;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    transform: translateY(-50%) scale(1.05);
}

.vs-carousel__arrow:active {
    transform: translateY(-50%) scale(0.96);
}

.vs-carousel__arrow--prev { left: 16px; }
.vs-carousel__arrow--next { right: 16px; }

.vs-carousel__arrow svg {
    width: 18px;
    height: 18px;
    stroke-width: 2.5;
}

/* Dots Indikator */
.vs-carousel__dots {
    position: absolute;
    bottom: 18px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-radius: 9999px;
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.vs-dot {
    width: 7px;
    height: 7px;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.4);
    border: 0;
    padding: 0;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(.4, 0, .2, 1);
}

.vs-dot:hover {
    background: rgba(255, 255, 255, 0.85);
}

.vs-dot.is-active {
    width: 24px;
    background: #ffffff;
    border-radius: 9999px;
}

/* Progress Bar Otomatis */
.vs-carousel__progress {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: rgba(255, 255, 255, 0.12);
    z-index: 10;
}

.vs-carousel__progress-bar {
    height: 100%;
    width: 0%;
    background: #ffffff;
    box-shadow: 0 0 8px rgba(255, 255, 255, 0.8);
}

/* Bar Keunggulan (Trust Bar dengan gaya .ad-stats Admin) */
.vs-trustbar {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    margin-top: 24px;
    margin-bottom: 36px;
}

.vs-trustbar__item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px 20px;
    border-radius: 14px;
    background: var(--surface);
    border: 1px solid var(--line);
    box-shadow: var(--shadow-sm);
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: default;
}

.vs-trustbar__item:hover {
    border-color: var(--line-strong);
    transform: translateY(-3px);
    box-shadow: var(--shadow-2);
}

.vs-trustbar__icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: var(--surface-2);
    border: 1px solid var(--line);
    color: var(--ink);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.2s ease;
}

.vs-trustbar__item:hover .vs-trustbar__icon {
    background: var(--ink);
    color: var(--bg);
    border-color: var(--ink);
}

.vs-trustbar__icon .vs-ico {
    width: 20px;
    height: 20px;
}

.vs-trustbar__text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.vs-trustbar__text b {
    font-family: var(--font-display);
    font-size: 13.5px;
    font-weight: 700;
    color: var(--ink);
}

.vs-trustbar__text small {
    font-size: 11.5px;
    color: var(--ink-soft);
    line-height: 1.35;
}

/* Category Grid Tiles (Gaya Admin Cards) */
.vs-catlist {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 14px;
}

.vs-catrow {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 20px;
    border-radius: 14px;
    background: var(--surface);
    border: 1px solid var(--line);
    box-shadow: var(--shadow-sm);
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.vs-catrow:hover {
    border-color: var(--line-strong);
    background: var(--surface-2);
    transform: translateY(-2px);
    box-shadow: var(--shadow-1);
}

.vs-catrow__icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: var(--surface-2);
    border: 1px solid var(--line);
    color: var(--ink);
    display: grid;
    place-items: center;
    flex-shrink: 0;
}

.vs-catrow__name {
    font-family: var(--font-display);
    font-size: 13.5px;
    font-weight: 700;
    color: var(--ink);
    flex: 1;
}

.vs-catrow__count {
    font-family: var(--font-display);
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    padding: 3px 8px;
    border-radius: 6px;
    background: var(--surface-2);
    border: 1px solid var(--line);
    color: var(--ink-soft);
}

.vs-catrow__arrow {
    color: var(--ink-mute);
    transition: transform 0.2s ease, color 0.2s ease;
}

.vs-catrow:hover .vs-catrow__arrow {
    color: var(--ink);
    transform: translateX(3px);
}
    text-decoration: none;
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}

.vs-catrow:hover {
    background: var(--bg-soft);
    border-color: var(--ink);
    transform: translateY(-2px);
    box-shadow: 0 10px 24px -4px rgba(15, 23, 42, 0.08);
}

.vs-catrow__icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: var(--surface-2);
    border: 1px solid var(--line);
    color: var(--ink);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.2s ease;
}

.vs-catrow:hover .vs-catrow__icon {
    background: var(--ink);
    color: var(--bg);
    transform: scale(1.05);
}

.vs-catrow__icon .vs-ico {
    width: 22px;
    height: 22px;
}

.vs-catrow__name {
    font-family: var(--font-display) !important;
    font-size: 15px;
    font-weight: 700;
    color: var(--ink) !important;
}

.vs-catrow__count {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--ink-mute);
    margin-left: auto;
    padding: 4px 10px;
    background: var(--surface-2);
    border-radius: 9999px;
    border: 1px solid var(--line);
}

.vs-catrow__arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ink-mute);
    transition: transform 0.2s ease, color 0.2s ease;
}

.vs-catrow:hover .vs-catrow__arrow {
    color: var(--ink);
    transform: translateX(3px);
}

/* Band Parallax Highlight */
.vs-band {
    position: relative;
    height: clamp(340px, 45vh, 460px);
    margin-top: 72px;
    overflow: hidden;
    color: #fff;
    display: grid;
    place-items: center;
    text-align: center;
    background: #0f172a;
    z-index: 2;
}

.vs-band__img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    opacity: 0.65;
}

.vs-band__shade {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.75) 0%, rgba(15, 23, 42, 0.88) 100%);
}

.vs-band__copy {
    position: relative;
    padding: 0 24px;
    max-width: 720px;
}

.vs-band__kicker {
    display: inline-block;
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #93c5fd;
    margin-bottom: 12px;
}

.vs-band__copy h2 {
    color: #ffffff !important;
    margin: 0 0 14px;
    font-size: clamp(26px, 3.4vw, 40px);
    font-weight: 800;
    letter-spacing: -0.02em;
}

.vs-band__copy p {
    color: #e2e8f0 !important;
    font-size: 15.5px;
    line-height: 1.6;
    margin: 0 auto 24px;
    max-width: 52ch;
}

.vs-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 13px 28px;
    border-radius: 9999px;
    background: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #ffffff !important;
    font-family: var(--font-display);
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.vs-cta:hover {
    background: #0f172a !important;
    color: #ffffff !important;
    border-color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
}

/* Values Section Modern Card Styling */
.vs-values {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 20px;
}

.vs-val {
    background: var(--surface);
    border: 1.5px solid var(--line);
    border-radius: 18px;
    padding: 28px 24px;
    text-align: left;
    box-shadow: var(--shadow-sm);
    transition: all 0.25s ease;
}

.vs-val:hover {
    border-color: var(--ink);
    box-shadow: 0 12px 28px -4px rgba(15, 23, 42, .08);
    transform: translateY(-4px);
}

.vs-val__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}

.vs-val__num {
    font-family: var(--font-display);
    font-size: 32px;
    font-weight: 800;
    color: var(--line-strong);
    line-height: 1;
    transition: color 0.2s ease;
}

.vs-val:hover .vs-val__num {
    color: var(--ink);
}

.vs-val__icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: var(--surface-2);
    border: 1px solid var(--line);
    color: var(--ink);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.vs-val:hover .vs-val__icon {
    background: var(--ink);
    color: var(--bg);
    transform: scale(1.05);
}

.vs-val h3 {
    font-family: var(--font-display) !important;
    font-size: 17px;
    font-weight: 700;
    color: var(--ink);
    margin: 0 0 8px;
}

.vs-val p {
    margin: 0;
    font-size: 14px;
    line-height: 1.6;
    color: var(--ink-soft);
}

@media (max-width: 1080px) {
    .vs-trustbar { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .vs-values { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (max-width: 768px) {
    .vs-carousel {
        height: 440px;
        border-radius: 16px;
    }
    .vs-carousel__arrow {
        display: none;
    }
    .vs-slide__content {
        padding: 0 16px;
    }
    .vs-slide__glass {
        padding: 24px 20px;
        border-radius: 16px;
    }
    .vs-slide__overlay {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.6) 0%, rgba(15, 23, 42, 0.92) 100%);
    }
    .vs-trustbar {
        grid-template-columns: 1fr;
        gap: 10px;
        margin-top: 16px;
    }
    .vs-values {
        grid-template-columns: 1fr;
    }
}

.ad-dot {
    width: 6px;
    height: 6px;
    border-radius: 9999px;
    background: var(--line-strong);
    border: 0;
    padding: 0;
    cursor: pointer;
    transition: all 0.25s ease;
}
.ad-dot.is-active {
    width: 20px;
    background: var(--ink);
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    const track = document.getElementById('ad-slider-track');
    if (!track) return;

    const slides = track.querySelectorAll('.ad-slide-item');
    const dots = document.querySelectorAll('#ad-slide-dots .ad-dot');
    const prevBtn = document.getElementById('ad-btn-prev');
    const nextBtn = document.getElementById('ad-btn-next');
    const box = document.getElementById('ad-slider-box');
    if (!slides.length) return;

    let current = 0;
    let timer = null;
    const intervalMs = 3500; // Slide otomatis setiap 3.5 detik

    function goTo(idx) {
        current = (idx + slides.length) % slides.length;
        track.style.transform = `translateX(-${current * 100}%)`;
        dots.forEach((d, i) => d.classList.toggle('is-active', i === current));
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    function start() {
        stop();
        timer = setInterval(next, intervalMs);
    }

    function stop() {
        if (timer) clearInterval(timer);
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            next();
            start();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            prev();
            start();
        });
    }

    dots.forEach((dot, idx) => {
        dot.addEventListener('click', function(e) {
            e.preventDefault();
            goTo(idx);
            start();
        });
    });

    if (box) {
        box.addEventListener('mouseenter', stop);
        box.addEventListener('mouseleave', start);

        let startX = 0;
        box.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
            stop();
        }, { passive: true });

        box.addEventListener('touchend', function(e) {
            const diffX = e.changedTouches[0].clientX - startX;
            if (Math.abs(diffX) > 35) {
                if (diffX < 0) next();
                else prev();
            }
            start();
        }, { passive: true });
    }

    goTo(0);
    start();
})();
</script>
@endpush
