@extends('admin.layout')

@section('title', $user->name)
@section('crumb', 'Penjualan / Pelanggan / '.$user->name)

@section('content')
    <div class="ad-head">
        <div>
            <h1>{{ $user->name }}</h1>
            <p>{{ $user->email }} · bergabung {{ $user->created_at?->format('d M Y') }}</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="vs-btn vs-btn--line">← Kembali</a>
    </div>

    <div class="ad-stats">
        <div class="ad-stat">
            <div class="ad-stat__label">Pesanan</div>
            <div class="ad-stat__value">{{ $stats['count'] }}</div>
        </div>
        <div class="ad-stat">
            <div class="ad-stat__label">Total belanja</div>
            <div class="ad-stat__value">Rp {{ number_format($stats['spent'], 0, ',', '.') }}</div>
        </div>
        <div class="ad-stat">
            <div class="ad-stat__label">Order terakhir</div>
            <div class="ad-stat__value">{{ $stats['last']?->format('d M Y') ?? '—' }}</div>
        </div>
        <div class="ad-stat">
            <div class="ad-stat__label">Rata-rata / order</div>
            <div class="ad-stat__value">
                Rp {{ number_format($stats['count'] ? (int) round($stats['spent'] / $stats['count']) : 0, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="ad-split ad-mt">
        <div class="ad-panel">
            <h2>Riwayat pesanan</h2>

            @if ($orders->isEmpty())
                <p class="ad-hint">Pelanggan ini belum pernah checkout.</p>
            @else
                <div class="ad-table-wrap ad-table-wrap--flush">
                    <table class="ad-table">
                        <thead>
                            <tr>
                                <th>Pesanan</th>
                                <th>Tanggal</th>
                                <th>Item</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td><span class="ad-name">#{{ $order->id }}</span></td>
                                    <td class="ad-muted">{{ $order->created_at?->format('d M Y') }}</td>
                                    <td class="ad-muted">{{ (int) $order->items_sum_qty }} pcs</td>
                                    <td><span class="ad-name">Rp {{ number_format($order->total, 0, ',', '.') }}</span></td>
                                    <td><span class="vs-pill vs-pill--{{ $order->statusTone() }}">{{ $order->statusLabel() }}</span></td>
                                    <td>
                                        <div class="ad-actions">
                                            <a class="ad-btn" href="{{ route('admin.orders.show', $order) }}">Buka</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="ad-panel">
            <h2>Percakapan terakhir</h2>

            @if ($chats->isEmpty())
                <p class="ad-hint">Belum ada percakapan dari pelanggan ini.</p>
            @else
                <div class="ad-thread ad-thread--compact">
                    @foreach ($chats as $message)
                        <div @class([
                            'ad-msg',
                            'ad-msg--user' => $message->role === 'user',
                            'ad-msg--admin' => $message->role !== 'user',
                        ])>
                            <span class="ad-msg__meta">
                                {{ $message->role === 'user' ? 'Pelanggan' : 'Asisten/Admin' }}
                                · {{ $message->created_at?->format('d/m H:i') }}
                            </span>
                            {{ $message->content }}
                        </div>
                    @endforeach
                </div>

                <div class="ad-pager">
                    <span></span>
                    <a class="ad-btn" href="{{ route('admin.chat.index') }}">Buka inbox chat →</a>
                </div>
            @endif
        </div>
    </div>
@endsection
