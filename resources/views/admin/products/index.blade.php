@extends('admin.layout')

@section('title', 'Daftar Produk')

@section('content')

@if ($missingShipping > 0)
    <div class="ad-panel" style="margin-bottom:16px">
        <b>{{ $missingShipping }} produk belum punya data berat &amp; ukuran kemasan.</b>
        <span class="ad-muted">Lengkapi lewat Edit produk supaya ongkir bisa dihitung otomatis nanti.</span>
    </div>
@endif
    <div class="ad-head">
        <div>
            <h1>Daftar <em>produk</em></h1>
            <p>{{ $products->count() }} produk di katalog · atur harga, stok, dan status tayang.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="vs-btn vs-btn--primary">+ Tambah produk</a>
    </div>

    @if ($products->isEmpty())
        <div class="vs-empty">
            <h3>Belum ada produk</h3>
            <p>Tambahkan produk pertama, fotonya bisa langsung dihapus background-nya dengan AI.</p>
            <a href="{{ route('admin.products.create') }}" class="vs-btn vs-btn--primary">Tambah produk →</a>
        </div>
    @else
        <div class="ad-table-wrap">
            <table class="ad-table">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Brand</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr @class(['is-inactive' => ! $product->is_active])>
                            <td>
                                <div class="ad-thumb">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->title }}" loading="lazy"
                                         onerror="this.onerror=null;this.src='{{ asset('img/no-image.png') }}'">
                                </div>
                            </td>

                            <td>
                                <div class="ad-name">{{ $product->title }}</div>
                                <div class="ad-muted">
                                    #{{ $product->id }}
                                    @if ($product->sku)
                                        · {{ $product->sku }}
                                    @endif
                                </div>
                            </td>

                            <td class="ad-muted">{{ $product->category?->title ?? '—' }}</td>
                            <td class="ad-muted">{{ $product->brand?->title ?? '—' }}</td>

                            <td>
                                <span class="ad-name">Rp {{ number_format($product->final_price, 0, ',', '.') }}</span>
                                @if ($product->discount_price && $product->discount_price < $product->price)
                                    <span class="ad-price-old">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                @endif
                            </td>

                            <td>
                                @if ($product->stock <= 0)
                                    <span class="vs-pill vs-pill--danger">Habis</span>
                                @elseif ($product->stock <= 3)
                                    <span class="vs-pill vs-pill--warn">{{ $product->stock }} pcs</span>
                                @else
                                    <span class="ad-muted">{{ $product->stock }} pcs</span>
                                @endif

                                @unless ($product->hasShippingData())
                                <br><small class="ad-muted">Berat/ukuran kosong</small>
                                     @endunless
                            </td>

                            <td>
                                @if ($product->is_active)
                                    <span class="vs-pill vs-pill--ok">Aktif</span>
                                @else
                                    <span class="vs-pill">Nonaktif</span>
                                @endif
                            </td>

                            <td>
                                <div class="ad-actions">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="ad-btn ad-btn--warn">
                                        <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20h4l10-10-4-4L4 16z"/><path d="M14 6l4 4"/></svg>
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.products.toggle', $product->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="ad-btn">
                                            {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}"
                                          onsubmit="return confirm('Hapus produk ini secara permanen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ad-btn ad-btn--danger">
                                            <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V5h6v2M6 7l1 13h10l1-13"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
