@props(['product'])

@php
    $discount = $product->discount_percent;
    $lowStock = $product->inStock() && $product->stock <= 5;

    $quick = [
        'name' => $product->title,
        'category' => $product->category?->title,
        'price' => 'Rp '.number_format($product->final_price, 0, ',', '.'),
        'old' => ($product->discount_price && $product->discount_price < $product->price) ? 'Rp '.number_format($product->price, 0, ',', '.') : null,
        'desc' => \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 220),
        'image' => $product->image_url,
        'model' => $product->model_url,
        'url' => route('products.show', $product),
        'cart' => route('cart.store', $product),
        'inStock' => $product->inStock(),
        'rating' => $product->rating_total > 0
            ? number_format($product->rating_average, 1, ',', '.').' ('.$product->rating_total.' ulasan)'
            : null,
    ];
@endphp

<article class="vs-card vs-reveal" data-quick="{{ json_encode($quick) }}">
    <a class="vs-card__media" href="{{ route('products.show', $product) }}" aria-label="{{ $product->title }}">
        <span class="vs-card__tags">
            @if (! $product->inStock())
                <span class="vs-badge vs-badge--out">Stok habis</span>
            @elseif ($discount)
                <span class="vs-badge vs-badge--sale">Hemat {{ $discount }}%</span>
            @elseif ($lowStock)
                <span class="vs-badge">Sisa {{ $product->stock }}</span>
            @endif
            @if ($product->hasModel())
                <span class="vs-badge vs-badge--360">
                    <svg class="vs-ico vs-ico--xs" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
                    Putar 360°
                </span>
            @endif
        </span>
        <span class="vs-card__tile">
            <img src="{{ $product->image_url }}" alt="{{ $product->title }}" loading="lazy"
                 onerror="this.onerror=null;this.src='{{ asset('img/no-image.png') }}'">
        </span>
        <span class="vs-card__peek" aria-hidden="true">
            <svg class="vs-ico vs-ico--sm" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/></svg>
            <span>Lihat Cepat</span>
        </span>
    </a>

    <div class="vs-card__body">
        <div class="vs-card__meta">
            <span class="vs-card__cat">{{ $product->category?->title ?? 'Furnitur' }}</span>
            @if ($product->brand)
                <span class="vs-card__sep">•</span>
                <span class="vs-card__brand">{{ $product->brand->title }}</span>
            @endif
        </div>

        <h3 class="vs-card__title">
            <a href="{{ route('products.show', $product) }}">{{ $product->title }}</a>
        </h3>

        <div class="vs-card__rating">
            @if ($product->rating_total > 0)
                <x-stars :value="$product->rating_average" />
                <span>{{ number_format($product->rating_average, 1, ',', '.') }} ({{ $product->rating_total }})</span>
            @else
                <span>Belum ada ulasan</span>
            @endif
        </div>

        <div class="vs-card__row">
            <div class="vs-price">
                <span class="vs-price__main">Rp {{ number_format($product->final_price, 0, ',', '.') }}</span>
                @if ($product->discount_price && $product->discount_price < $product->price)
                    <s class="vs-price-old">Rp {{ number_format($product->price, 0, ',', '.') }}</s>
                @endif
            </div>

            @auth
                @if ($product->inStock())
                    <form method="POST" action="{{ route('cart.store', $product) }}" data-cart-form>
                        @csrf
                        <button type="submit" class="vs-add" aria-label="Tambah {{ $product->title }} ke keranjang">
                            <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                            <span>Tambah</span>
                        </button>
                    </form>
                @else
                    <button type="button" class="vs-add" disabled aria-label="Stok {{ $product->title }} habis">Habis</button>
                @endif
            @else
                <a href="{{ route('login') }}" class="vs-add" aria-label="Masuk untuk membeli {{ $product->title }}">
                    <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Tambah</span>
                </a>
            @endauth
        </div>
    </div>
</article>
