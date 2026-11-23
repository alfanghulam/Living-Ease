@extends('layouts.app')

@section('title', $product->title . ' — LivingEase')
@section('meta_description', Str::limit(strip_tags($product->description), 150))

@push('scripts')
    @if ($product->hasModel())
        <script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.0.0/dist/model-viewer.min.js"></script>
    @endif
@endpush

@section('content')
    @php
        $avg = $product->reviews_avg_rating ? round((float) $product->reviews_avg_rating, 1) : null;
        $total = (int) $product->reviews_count;
    @endphp

    <div class="vs-shell" style="padding-top: 24px; padding-bottom: 60px;">
        <nav class="ad-crumb" aria-label="Breadcrumb" style="margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('welcome') }}" style="color: var(--ink-soft); text-decoration: none;">BERANDA</a>
            <span>/</span>
            <a href="{{ route('home', ['cat_id' => $product->category_id]) }}" style="color: var(--ink-soft); text-decoration: none;">{{ strtoupper($product->category?->title ?? 'PRODUK') }}</a>
            <span>/</span>
            <span style="color: var(--ink); font-weight: 700;">{{ strtoupper($product->title) }}</span>
        </nav>

        @if (session('success'))
            <div class="vs-alert vs-alert--ok">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="vs-alert vs-alert--err">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="vs-errors">
                @foreach ($errors->all() as $error)
                    <span>{{ $error }}</span>
                @endforeach
            </div>
        @endif

        <div class="vs-pdp" data-pdp>
            <div class="vs-pdp__media" data-viewer>
                <div class="vs-pdp__stage" data-pane="photo" @if ($product->hasModel()) hidden @endif>
                    <img src="{{ $product->image_url }}" alt="{{ $product->title }}"
                         onerror="this.onerror=null;this.src='{{ asset('img/no-image.png') }}'">
                </div>

                @if ($product->hasModel())
                    <div class="vs-pdp__stage" data-pane="360">
                        <model-viewer src="{{ $product->model_url }}"
                                      poster="{{ $product->image_url }}"
                                      alt="Model 3D {{ $product->title }}"
                                      camera-controls
                                      disable-zoom
                                      interaction-prompt="auto"
                                      shadow-intensity="1"
                                      environment-image="neutral"
                                      camera-orbit="-25deg 75deg auto"
                                      interpolation-decay="80"
                                      data-model></model-viewer>
                        <p class="vs-pdp__hint">Seret untuk memutar ke segala arah.</p>
                    </div>

                    <div class="vs-pdp__bar">
                        <div class="vs-seg" role="tablist" aria-label="Tampilan produk">
                            <button type="button" role="tab" aria-selected="true" data-tab="360">Putar 360°</button>
                            <button type="button" role="tab" aria-selected="false" data-tab="photo">Foto</button>
                        </div>

                        <div class="vs-orbit" data-orbit-bar aria-label="Lihat dari sisi">
                            <button type="button" data-orbit="0deg 90deg auto">Depan</button>
                            <button type="button" data-orbit="90deg 90deg auto">Kanan</button>
                            <button type="button" data-orbit="180deg 90deg auto">Belakang</button>
                            <button type="button" data-orbit="-90deg 90deg auto">Kiri</button>
                            <button type="button" data-orbit="0deg 0deg auto">Atas</button>
                            <button type="button" data-orbit="0deg 180deg auto">Bawah</button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="vs-pdp__info">
                <h1>{{ $product->title }}</h1>

                <div class="vs-rating">
                    @if ($total > 0)
                        <x-stars :value="$avg" />
                        <b>{{ number_format($avg, 1, ',', '.') }}</b>
                        <a href="#ulasan">{{ $total }} ulasan</a>
                    @else
                        <a href="#ulasan">Belum ada ulasan</a>
                    @endif
                </div>

                <div class="vs-pdp__price">
                    <b>Rp {{ number_format($product->final_price, 0, ',', '.') }}</b>
                    @if ($product->discount_price && $product->discount_price < $product->price)
                        <s>Rp {{ number_format($product->price, 0, ',', '.') }}</s>
                        <i>Hemat {{ $product->discount_percent }}%</i>
                    @endif
                </div>

                @if ($product->inStock())
                    <span class="vs-stock"><span class="vs-dot"></span>Tersedia, sisa {{ $product->stock }}</span>
                @else
                    <span class="vs-stock vs-stock--out"><span class="vs-dot"></span>Stok habis</span>
                @endif

                <p class="vs-desc">{{ $product->description }}</p>

                <div class="vs-specs">
                    <div><span>Brand</span><b>{{ $product->brand?->title ?? '-' }}</b></div>
                    <div><span>Kategori</span><b>{{ $product->category?->title ?? '-' }}</b></div>
                    @if ($product->sku)
                        <div><span>SKU</span><b>{{ $product->sku }}</b></div>
                    @endif
                </div>

                @auth
                    @if ($product->inStock())
                        <div class="vs-pdp__actions">
                            <div class="vs-qty" data-qty data-min="1" data-max="{{ max(1, $product->stock) }}">
                                <button type="button" data-qty-step="-1" aria-label="Kurangi jumlah">−</button>
                                <input type="number" value="1" min="1" max="{{ max(1, $product->stock) }}" data-qty-input aria-label="Jumlah">
                                <button type="button" data-qty-step="1" aria-label="Tambah jumlah">+</button>
                            </div>

                            <form method="POST" action="{{ route('cart.store', $product) }}" data-cart-form>
                                @csrf
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="vs-btn vs-btn--line">Tambah ke keranjang</button>
                            </form>

                            <form method="POST" action="{{ route('checkout.buyNow', $product) }}">
                                @csrf
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="vs-btn vs-btn--primary">Beli sekarang</button>
                            </form>
                        </div>
                    @else
                        <div class="vs-pdp__actions">
                            <a href="{{ route('home') }}" class="vs-btn vs-btn--line">Cari produk lain</a>
                        </div>
                    @endif
                @else
                    <div class="vs-pdp__actions">
                        <a href="{{ route('login') }}" class="vs-btn vs-btn--primary">Masuk untuk membeli</a>
                        <a href="{{ route('register') }}" class="vs-btn vs-btn--line">Daftar akun</a>
                    </div>
                @endauth
            </div>
        </div>

        {{-- ULASAN --}}
        <section class="vs-section" id="ulasan">
            <div class="vs-sechead vs-reveal">
                <div>
                    <h2>Ulasan pembeli</h2>
                    <p>Hanya pembeli dengan pesanan selesai yang bisa memberi ulasan.</p>
                </div>
            </div>

            <div class="vs-reviews vs-reveal">
                <div class="vs-reviews__sum">
                    @if ($total > 0)
                        <div class="vs-reviews__avg">{{ number_format($avg, 1, ',', '.') }}</div>
                        <x-stars :value="$avg" />
                        <p class="vs-reviews__count">{{ $total }} ulasan</p>

                        <div class="vs-dist">
                            @foreach ([5, 4, 3, 2, 1] as $star)
                                @php $n = (int) ($distribution[$star] ?? 0); @endphp
                                <div class="vs-dist__row">
                                    <span>{{ $star }}</span>
                                    <span class="vs-dist__bar"><i style="width: {{ $total ? $n / $total * 100 : 0 }}%"></i></span>
                                    <span>{{ $n }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="vs-reviews__count">Belum ada ulasan untuk produk ini.</p>
                    @endif
                </div>

                <div class="vs-reviews__main">
                    @auth
                        @if ($canReview)
                            <form method="POST" action="{{ route('reviews.store', $product) }}" class="vs-panel vs-reviewform">
                                @csrf
                                <h3>{{ $myReview ? 'Perbarui ulasanmu' : 'Tulis ulasan' }}</h3>

                                <fieldset class="vs-rate">
                                    <legend>Rating</legend>
                                    <div class="vs-rate-input">
                                        @foreach ([5, 4, 3, 2, 1] as $star)
                                            <input type="radio" id="star{{ $star }}" name="rating" value="{{ $star }}"
                                                   @checked((int) old('rating', $myReview?->rating) === $star) required>
                                            <label for="star{{ $star }}" title="{{ $star }} bintang"><span class="vs-sr">{{ $star }} bintang</span>★</label>
                                        @endforeach
                                    </div>
                                </fieldset>

                                <div class="vs-field">
                                    <label for="comment">Komentar (opsional)</label>
                                    <textarea id="comment" name="comment" class="vs-input" rows="3" maxlength="1000"
                                              placeholder="Ceritakan pengalamanmu memakai produk ini">{{ old('comment', $myReview?->comment) }}</textarea>
                                </div>

                                <button type="submit" class="vs-btn vs-btn--primary">{{ $myReview ? 'Simpan perubahan' : 'Kirim ulasan' }}</button>
                            </form>
                        @else
                            <p class="vs-reviews__note">Kamu bisa menulis ulasan setelah pesanan produk ini berstatus selesai.</p>
                        @endif
                    @else
                        <p class="vs-reviews__note"><a href="{{ route('login') }}">Masuk</a> untuk menulis ulasan setelah pesananmu selesai.</p>
                    @endauth

                    <div class="vs-reviewlist">
                        @foreach ($reviews as $review)
                            <article class="vs-review">
                                <header>
                                    <x-stars :value="$review->rating" />
                                    <b>{{ $review->displayName() }}</b>
                                    <span class="vs-review__badge">Pembeli terverifikasi</span>
                                    <time datetime="{{ $review->created_at->toDateString() }}">{{ $review->created_at->translatedFormat('d M Y') }}</time>
                                </header>
                                @if ($review->comment)
                                    <p>{{ $review->comment }}</p>
                                @endif
                                @auth
                                    @if ($review->user_id === auth()->id())
                                        <form method="POST" action="{{ route('reviews.destroy', $review) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="vs-link vs-link--btn">Hapus ulasanku</button>
                                        </form>
                                    @endif
                                @endauth
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        @if ($related->isNotEmpty())
            <section class="vs-section">
                <div class="vs-sechead vs-reveal">
                    <div>
                        <h2>Produk serupa</h2>
                        <p>Masih dari kategori {{ $product->category?->title }}</p>
                    </div>
                    <a class="vs-link" href="{{ route('home', ['cat_id' => $product->category_id]) }}">Lihat semua</a>
                </div>

                <div class="vs-grid">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
