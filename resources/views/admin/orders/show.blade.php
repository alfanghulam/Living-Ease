@extends('admin.layout')

@section('title', 'Pesanan #'.$order->id)
@section('crumb', 'Penjualan / Pesanan / #'.$order->id)

@section('content')
    <div class="ad-head">
        <div>
            <h1>Pesanan <em>#{{ $order->id }}</em></h1>
            <p>
                {{ $order->created_at?->format('d M Y, H:i') }} ·
                {{ $order->items->sum('qty') }} item ·
                <span class="vs-pill vs-pill--{{ $order->statusTone() }}">{{ $order->statusLabel() }}</span>
            </p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="vs-btn vs-btn--line">← Kembali</a>
    </div>

    <div class="ad-order">
        <div class="ad-panel">
            <h2>Item pesanan</h2>

            <div class="ad-table-wrap ad-table-wrap--flush">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <div class="ad-order-product">
                                        <span class="ad-thumb">
                                            <img src="{{ $item->imageUrl() }}" alt=""
                                                 onerror="this.onerror=null;this.src='{{ asset('img/no-image.png') }}'">
                                        </span>
                                        <span>
                                            <span class="ad-name">{{ $item->title() }}</span><br>
                                            <span class="ad-muted">#{{ $item->product_id }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td class="ad-muted">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="ad-muted">{{ $item->qty }}</td>
                                <td><span class="ad-name">Rp {{ number_format($item->subtotal(), 0, ',', '.') }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($order->discount_amount > 0)
                <div class="ad-kv">
                    <div><span>Diskon ({{ $order->promo_code }})</span><b>− Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</b></div>
                </div>
            @endif

            <div class="ad-kv ad-kv--total">
                <div><span>Total pesanan</span><b>Rp {{ number_format($order->total, 0, ',', '.') }}</b></div>
            </div>

            @if ($order->note)
                <div class="ad-note">
                    <strong>Catatan internal</strong>
                    <pre>{{ $order->note }}</pre>
                </div>
            @endif
        </div>

        <div class="ad-stack">
            <div class="ad-panel">
                <h2>Pelanggan &amp; pengiriman</h2>

                <div class="ad-kv">
                    <div><span>Nama</span><b>{{ $order->full_name }}</b></div>
                    <div><span>Email</span><b>{{ $order->email }}</b></div>
                    <div><span>Telepon</span><b>{{ $order->phone ?: '—' }}</b></div>
                    <div><span>Alamat</span><b>{{ $order->address }}</b></div>
                    <div><span>Kota</span><b>{{ $order->city }}</b></div>
                    <div><span>Provinsi</span><b>{{ $order->state }}</b></div>
                    <div><span>Kode pos</span><b>{{ $order->zip }}</b></div>
                    <div><span>Resi</span><b>{{ $order->tracking_number ?: '—' }}</b></div>
                    <div><span>Metode bayar</span><b>{{ $order->paymentLabel() ?: '—' }}</b></div>
                    @if ($order->user)
                        <div><span>Akun</span><b>{{ $order->user->email }}</b></div>
                    @endif
                </div>
            </div>

            <div class="ad-panel">
                <h2>Ubah status</h2>

                @if (count($order->nextStatuses()))
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="ad-status-form">
                        @csrf
                        @method('PATCH')

                        <div class="vs-field">
                            <label for="status">Status baru</label>
                            <select id="status" class="vs-input" name="status" required>
                                @foreach ($order->nextStatuses() as $next)
                                    <option value="{{ $next }}">{{ \App\Models\Order::STATUS_LABELS[$next] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="vs-field">
                            <label for="tracking_number">Nomor resi (opsional)</label>
                            <input id="tracking_number" class="vs-input" type="text" name="tracking_number"
                                   value="{{ $order->tracking_number }}" placeholder="JNE / J&T / SiCepat...">
                        </div>

                        <div class="vs-field">
                            <label for="note">Catatan internal (opsional)</label>
                            <textarea id="note" class="vs-input" name="note" rows="2"
                                      placeholder="Misal: pembayaran dikonfirmasi via transfer BCA"></textarea>
                        </div>

                        <button type="submit" class="vs-btn vs-btn--primary vs-btn--block">Simpan status</button>

                        @if (in_array('paid', $order->nextStatuses()))
                            <p class="ad-hint">Menandai “Dibayar” akan mengurangi stok produk otomatis.</p>
                        @endif
                    </form>
                @else
                    <p class="ad-hint">Status <b>{{ $order->statusLabel() }}</b> sudah final, tidak ada perubahan lagi.</p>
                @endif
            </div>

            <div class="ad-panel">
                <h2>Timeline</h2>

                <div class="ad-timeline">
                    <div class="ad-step is-done">
                        <span class="ad-step__dot"></span>
                        Pesanan dibuat
                        <time>{{ $order->created_at?->format('d/m H:i') }}</time>
                    </div>

                    <div @class(['ad-step', 'is-done' => $order->paid_at, 'is-current' => $order->status === 'paid'])>
                        <span class="ad-step__dot"></span>
                        Pembayaran diterima
                        <time>{{ $order->paid_at?->format('d/m H:i') ?? '—' }}</time>
                    </div>

                    <div @class(['ad-step', 'is-done' => $order->shipped_at, 'is-current' => $order->status === 'shipped'])>
                        <span class="ad-step__dot"></span>
                        Dikirim
                        <time>{{ $order->shipped_at?->format('d/m H:i') ?? '—' }}</time>
                    </div>

                    <div @class(['ad-step', 'is-done' => $order->completed_at, 'is-current' => $order->status === 'completed'])>
                        <span class="ad-step__dot"></span>
                        Selesai
                        <time>{{ $order->completed_at?->format('d/m H:i') ?? '—' }}</time>
                    </div>

                    @if ($order->cancelled_at)
                        <div class="ad-step is-current">
                            <span class="ad-step__dot"></span>
                            Dibatalkan
                            <time>{{ $order->cancelled_at->format('d/m H:i') }}</time>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
