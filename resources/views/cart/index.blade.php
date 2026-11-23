@extends('layouts.app')

@section('title', 'Keranjang Belanja — LivingEase')

@section('content')
    <div class="vs-shell">
        <div class="vs-sechead vs-section--first vs-reveal">
            <div>
                <h2>Keranjang belanja</h2>
                <p>{{ $items->count() }} produk di keranjangmu</p>
            </div>
        </div>

        @if (session('success'))
            <div class="vs-alert vs-alert--ok">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="vs-alert vs-alert--err">{{ session('error') }}</div>
        @endif

        @if ($items->isEmpty())
            <div class="vs-empty">
                <h3>Keranjang masih kosong</h3>
                <p>Belum ada produk di sini. Yuk pilih yang kamu suka dulu.</p>
                <a href="{{ route('home') }}" class="vs-btn vs-btn--primary">Mulai belanja →</a>
            </div>
        @else
            <div class="vs-lines">
                @foreach ($items as $item)
                    <div class="vs-line">
                        <a class="vs-line__media" href="{{ route('products.show', $item->product) }}">
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->title }}"
                                 onerror="this.onerror=null;this.src='{{ asset('img/no-image.png') }}'">
                        </a>

                        <div>
                            <h3 class="vs-line__title">
                                <a href="{{ route('products.show', $item->product) }}">{{ $item->product->title }}</a>
                            </h3>
                            <p class="vs-line__meta">
                                Rp {{ number_format($item->product->final_price, 0, ',', '.') }}
                                · {{ $item->product->brand?->title }}
                            </p>

                            <div class="vs-line__controls">
                                <form method="POST" action="{{ route('cart.update', $item) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="vs-qty" data-qty data-min="1" data-max="50">
                                        <button type="button" data-qty-step="-1" aria-label="Kurangi jumlah">−</button>
                                        <input type="number" name="qty" value="{{ $item->qty }}" min="1" max="50"
                                               data-qty-input data-auto-submit aria-label="Jumlah {{ $item->product->title }}">
                                        <button type="button" data-qty-step="1" aria-label="Tambah jumlah">+</button>
                                    </div>
                                </form>

                                <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="vs-line__remove" aria-label="Hapus {{ $item->product->title }}">
                                        <svg class="vs-ico vs-ico--sm" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M4 7h16M9 7V5h6v2M6 7l1 13h10l1-13"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="vs-line__subtotal">
                            Rp {{ number_format($item->qty * $item->product->final_price, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="vs-summary">
                <div class="vs-summary__total">
                    <small>Total bayar</small>
                    Rp {{ number_format($total, 0, ',', '.') }}
                </div>

                <div class="vs-actions">
                    <a href="{{ route('home') }}" class="vs-btn vs-btn--line">Lanjut belanja</a>
                    <a href="{{ route('checkout.show') }}" class="vs-btn vs-btn--primary">Checkout →</a>
                </div>
            </div>
        @endif
    </div>
@endsection
