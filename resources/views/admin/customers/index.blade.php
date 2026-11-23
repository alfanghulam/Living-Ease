@extends('admin.layout')

@section('title', 'Pelanggan')
@section('crumb', 'Penjualan / Pelanggan')

@section('content')
    <div class="ad-head">
        <div>
            <h1>Pelanggan</h1>
            <p>{{ $totalCustomers }} akun terdaftar · lihat riwayat belanja dan percakapan.</p>
        </div>
    </div>

    <form class="ad-toolbar" method="GET" action="{{ route('admin.customers.index') }}">
        <input class="vs-input" type="search" name="q" value="{{ $search }}"
               placeholder="Cari nama atau email pelanggan...">
        <button class="ad-btn" type="submit">Cari</button>
        @if ($search)
            <a class="ad-btn" href="{{ route('admin.customers.index') }}">Reset</a>
        @endif
    </form>

    @if ($customers->isEmpty())
        <div class="vs-empty">
            <h3>Tidak ada pelanggan</h3>
            <p>Belum ada akun pembeli yang cocok dengan pencarian.</p>
        </div>
    @else
        <div class="ad-table-wrap">
            <table class="ad-table">
                <thead>
                    <tr>
                        <th>Pelanggan</th>
                        <th>Pesanan</th>
                        <th>Total belanja</th>
                        <th>Terakhir order</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr>
                            <td>
                                <div class="ad-name">{{ $customer->name }}</div>
                                <div class="ad-muted">{{ $customer->email }}</div>
                            </td>
                            <td class="ad-muted">{{ $customer->orders_count }} pesanan</td>
                            <td>
                                <span class="ad-name">Rp {{ number_format((int) $customer->orders_sum_total, 0, ',', '.') }}</span>
                            </td>
                            <td class="ad-muted">
                                {{ isset($lastOrders[$customer->id]) ? \Illuminate\Support\Carbon::parse($lastOrders[$customer->id])->format('d M Y') : '—' }}
                            </td>
                            <td>
                                <div class="ad-actions">
                                    <a class="ad-btn" href="{{ route('admin.customers.show', $customer) }}">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="ad-pager">
            <span>Menampilkan {{ $customers->firstItem() }}–{{ $customers->lastItem() }} dari {{ $customers->total() }} pelanggan</span>
            <div class="ad-pager__links">
                @if (! $customers->onFirstPage())
                    <a class="ad-btn" href="{{ $customers->previousPageUrl() }}">← Sebelumnya</a>
                @endif
                <span class="ad-muted">Hal {{ $customers->currentPage() }} / {{ $customers->lastPage() }}</span>
                @if ($customers->hasMorePages())
                    <a class="ad-btn" href="{{ $customers->nextPageUrl() }}">Berikutnya →</a>
                @endif
            </div>
        </div>
    @endif
@endsection
