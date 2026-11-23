@extends('layouts.app')

@section('title', 'Katalog Produk — LivingEase')
@section('meta_description', 'Jelajahi semua produk LivingEase: perabot modern, klasik, dan peralatan rumah.')

@section('content')
    <div class="vs-shell" style="padding-top: 24px; padding-bottom: 60px;">
        <div class="ad-head">
            <div>
                <span class="ad-crumb" style="display:inline-block; margin-bottom: 6px;">KATALOG · LIVING COLLECTION</span>
                <h1>Koleksi <em>Produk</em></h1>
                <p>
                    Menampilkan {{ $products->count() }} produk tersedia
                    @if (request('q'))
                        untuk pencarian “{{ request('q') }}”
                    @endif
                </p>
            </div>

            @if (request('q') || request('cat_id') || request('brand_id'))
                <a href="{{ route('home') }}" class="ad-btn">Reset Filter</a>
            @endif
        </div>

        {{-- Filter & Search Toolbar (Gaya Admin Toolbar) --}}
        <div class="ad-panel" style="padding: 16px 18px; margin-bottom: 24px;">
            <form class="vs-searchbar" id="cari" action="{{ route('home') }}" method="GET" role="search" style="margin-bottom: 14px; max-width: 100%;">
                @if (request('cat_id'))<input type="hidden" name="cat_id" value="{{ request('cat_id') }}">@endif
                @if (request('brand_id'))<input type="hidden" name="brand_id" value="{{ request('brand_id') }}">@endif
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama perabot, sofa, meja makan..." aria-label="Cari perabot">
                <button type="submit" class="vs-btn vs-btn--primary">
                    <svg class="vs-ico vs-ico--sm" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/></svg>
                    <span>Cari</span>
                </button>
            </form>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <div class="vs-filters" style="margin-bottom: 0;">
                    <span style="font-family: var(--font-display); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--ink-mute); align-self: center; margin-right: 4px;">Kategori:</span>
                    <a href="{{ route('home', array_filter(['brand_id' => request('brand_id'), 'q' => request('q')])) }}"
                       @class(['vs-filter', 'is-on' => ! request('cat_id')])>Semua</a>

                    @foreach ($navCategories as $category)
                        <a href="{{ route('home', array_filter(['cat_id' => $category->id, 'brand_id' => request('brand_id'), 'q' => request('q')])) }}"
                           @class(['vs-filter', 'is-on' => (int) request('cat_id') === $category->id])>
                            {{ $category->title }}
                        </a>
                    @endforeach
                </div>

                @if ($navBrands->isNotEmpty())
                    <div class="vs-filters" style="margin-bottom: 0; padding-top: 8px; border-top: 1px dashed var(--line);">
                        <span style="font-family: var(--font-display); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--ink-mute); align-self: center; margin-right: 4px;">Brand:</span>
                        <a href="{{ route('home', array_filter(['cat_id' => request('cat_id'), 'q' => request('q')])) }}"
                           @class(['vs-filter', 'is-on' => ! request('brand_id')])>Semua</a>

                        @foreach ($navBrands as $brand)
                            <a href="{{ route('home', array_filter(['brand_id' => $brand->id, 'cat_id' => request('cat_id'), 'q' => request('q')])) }}"
                               @class(['vs-filter', 'is-on' => (int) request('brand_id') === $brand->id])>
                                {{ $brand->title }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if (request('q'))
            <p style="font-size: 13px; color: var(--ink-soft); margin-bottom: 16px;">
                Hasil pencarian untuk <b>“{{ request('q') }}”</b>.
                <a href="{{ route('home', array_filter(['cat_id' => request('cat_id'), 'brand_id' => request('brand_id')])) }}" style="color: var(--accent); text-decoration: underline; margin-left: 6px;">Hapus pencarian</a>
            </p>
        @endif

        <div class="vs-grid">
            @forelse ($products as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="ad-panel" style="grid-column: 1 / -1; text-align: center; padding: 48px 20px;">
                    <h3 style="font-family: var(--font-display); font-size: 17px; font-weight: 700; margin: 0 0 6px;">Produk Tidak Ditemukan</h3>
                    <p style="font-size: 13px; color: var(--ink-soft); margin: 0 0 16px;">Coba gunakan kata kunci lain, atau reset filter untuk melihat semua produk.</p>
                    <a href="{{ route('home') }}" class="vs-btn vs-btn--primary">Reset Filter Produk</a>
                </div>
            @endforelse
        </div>

        @if (($storeSettings['promo_active'] ?? '0') === '1')
            <div class="ad-panel" id="promo" style="margin-top: 36px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; background: var(--surface-2);">
                <div>
                    <span class="ad-crumb" style="color: var(--accent); font-weight: 700; margin-bottom: 4px; display: block;">PROMO VOUCHER KHUSUS</span>
                    <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; margin: 0 0 4px; color: var(--ink);">
                        Diskon {{ $storeSettings['promo_percent'] ?? '40' }}% saat Checkout
                    </h2>
                    <p style="font-size: 12.5px; color: var(--ink-soft); margin: 0;">Salin kode voucher di samping dan tempelkan di halaman checkout.</p>
                </div>
                <div style="padding: 8px 16px; border-radius: 8px; border: 1px dashed var(--ink); background: var(--surface); font-family: var(--font-display); font-size: 16px; font-weight: 700; letter-spacing: 1.5px; color: var(--ink);">
                    {{ $storeSettings['promo_code'] ?? 'GERCEP40' }}
                </div>
            </div>
        @endif
    </div>
@endsection
