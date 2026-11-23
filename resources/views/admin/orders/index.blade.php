@extends('admin.layout')

@section('title', 'Pesanan')
@section('crumb', 'Penjualan / Pesanan')

@section('content')
    <div class="ad-head">
        <div>
            <h1>Pesanan</h1>
            <p>{{ $totalAll }} pesanan total · ubah status, isi resi, dan pantau pengiriman.</p>
        </div>
        <a href="{{ route('admin.orders.export', array_filter(request()->only('status', 'q'))) }}" class="vs-btn vs-btn--line">
            Export CSV
        </a>
    </div>

    <div class="ad-tabs">
        <a href="{{ route('admin.orders.index', array_filter(request()->only('q'))) }}"
           @class(['ad-tab', 'is-active' => ! $status])>
            Semua <b>{{ $totalAll }}</b>
        </a>

        @foreach (\App\Models\Order::STATUS_LABELS as $key => $label)
            <a href="{{ route('admin.orders.index', array_filter(['status' => $key, 'q' => request('q')])) }}"
               @class(['ad-tab', 'is-active' => $status === $key])>
                {{ $label }} <b>{{ $counts[$key] ?? 0 }}</b>
            </a>
        @endforeach
    </div>

    <form class="ad-toolbar" method="GET" action="{{ route('admin.orders.index') }}">
        @if ($status)
            <input type="hidden" name="status" value="{{ $status }}">
        @endif

        <input class="vs-input" type="search" name="q" value="{{ $search }}"
               placeholder="Cari #ID, nama, email, atau nomor resi...">

        <button class="ad-btn" type="submit">Cari</button>

        @if ($search)
            <a class="ad-btn" href="{{ route('admin.orders.index', array_filter(request()->only('status'))) }}">Reset</a>
        @endif
    </form>

    @if ($orders->isEmpty())
        <div class="vs-empty">
            <h3>Tidak ada pesanan</h3>
            <p>Belum ada pesanan yang cocok dengan filter ini.</p>
            <a href="{{ route('admin.orders.index') }}" class="vs-btn vs-btn--line">Lihat semua pesanan</a>
        </div>
    @else
        <div class="ad-table-wrap">
            <table class="ad-table">
                <thead>
                    <tr>
                        <th>Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Item</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>
                                <a class="ad-name" href="{{ route('admin.orders.show', $order) }}">#{{ $order->id }}</a>
                                <div class="ad-muted">{{ $order->city }}</div>
                            </td>
                            <td>
                                <div class="ad-name">{{ $order->full_name }}</div>
                                <div class="ad-muted">{{ $order->email }}</div>
                            </td>
                            <td class="ad-muted">{{ $order->created_at?->format('d M Y H:i') }}</td>
                            <td class="ad-muted">{{ (int) $order->items_sum_qty }} pcs</td>
                            <td><span class="ad-name">Rp {{ number_format($order->total, 0, ',', '.') }}</span></td>
                            <td>
                                <span class="vs-pill vs-pill--{{ $order->statusTone() }}">{{ $order->statusLabel() }}</span>
                            </td>
                            <td>
                                <div class="ad-actions">
                                    <a class="ad-btn" href="{{ route('admin.orders.show', $order) }}">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="ad-pager">
            <span>
                Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan
            </span>

            <div class="ad-pager__links">
                @if (! $orders->onFirstPage())
                    <a class="ad-btn" href="{{ $orders->previousPageUrl() }}">← Sebelumnya</a>
                @endif

                <span class="ad-muted">Hal {{ $orders->currentPage() }} / {{ $orders->lastPage() }}</span>

                @if ($orders->hasMorePages())
                    <a class="ad-btn" href="{{ $orders->nextPageUrl() }}">Berikutnya →</a>
                @endif
            </div>
        </div>
    @endif
@endsection
