@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="ad-head">
        <div>
            <h1>Dashboard <em>overview</em></h1>
            <p>Ringkasan penjualan dan aktivitas toko hari ini.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="vs-btn vs-btn--primary">Kelola produk →</a>
    </div>

    <div class="ad-stats">
        <div class="ad-stat">
            <span class="ad-stat__icon">
                <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19h16M6 19V9m4 10V5m4 14v-7m4 7V8"/></svg>
            </span>
            <div class="ad-stat__label">Total penjualan</div>
            <div class="ad-stat__value">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
        </div>

        <div class="ad-stat">
            <span class="ad-stat__icon">
                <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 7h12l1 13H5z"/><path d="M9 7V5a3 3 0 0 1 6 0v2"/></svg>
            </span>
            <div class="ad-stat__label">Total pesanan</div>
            <div class="ad-stat__value">{{ $totalOrders }}</div>
        </div>

        <div class="ad-stat">
            <span class="ad-stat__icon">
                <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 11h18"/></svg>
            </span>
            <div class="ad-stat__label">Pesanan 7 hari</div>
            <div class="ad-stat__value">{{ $ordersThisWeek }}</div>
        </div>

        <div class="ad-stat">
            <span class="ad-stat__icon">
                <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3.4"/><path d="M3 20a6 6 0 0 1 12 0"/><path d="M16 5.5a3 3 0 0 1 0 5.6M18 20a5.5 5.5 0 0 0-3-4.9"/></svg>
            </span>
            <div class="ad-stat__label">Total user</div>
            <div class="ad-stat__value">{{ $totalUsers }}</div>
        </div>
    </div>

    <div class="ad-grid2">
        <div class="ad-panel">
            <h2><span class="vs-dot"></span> Perlu tindakan</h2>

            <div class="ad-tasks">
                <a class="ad-task" href="{{ route('admin.orders.index', ['status' => 'pending']) }}">
                    <span class="ad-task__n {{ $pendingOrders ? 'is-hot' : '' }}">{{ $pendingOrders }}</span>
                    <span>Pesanan menunggu pembayaran</span>
                </a>

                <a class="ad-task" href="{{ route('admin.products.index') }}">
                    <span class="ad-task__n {{ $lowStock ? 'is-warn' : '' }}">{{ $lowStock }}</span>
                    <span>Produk dengan stok ≤ 3</span>
                </a>

                <a class="ad-task" href="{{ route('admin.orders.index') }}">
                    <span class="ad-task__n">{{ $ordersThisWeek }}</span>
                    <span>Pesanan masuk 7 hari terakhir</span>
                </a>
            </div>
        </div>

        <div class="ad-panel">
            <h2>Pesanan terbaru</h2>

            @if ($recentOrders->isEmpty())
                <p class="ad-hint">Belum ada pesanan masuk.</p>
            @else
                <div class="ad-table-wrap ad-table-wrap--flush">
                    <table class="ad-table ad-table--compact">
                        <tbody>
                            @foreach ($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a class="ad-name" href="{{ route('admin.orders.show', $order) }}">#{{ $order->id }}</a>
                                        <div class="ad-muted">{{ $order->full_name }}</div>
                                    </td>
                                    <td class="ad-muted">{{ $order->created_at?->format('d M H:i') }}</td>
                                    <td><span class="ad-name">Rp {{ number_format($order->total, 0, ',', '.') }}</span></td>
                                    <td>
                                        <span class="vs-pill vs-pill--{{ $order->statusTone() }}">{{ $order->statusLabel() }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="ad-pager">
                    <span></span>
                    <a class="ad-btn" href="{{ route('admin.orders.index') }}">Semua pesanan →</a>
                </div>
            @endif
        </div>
    </div>

    <div class="ad-panel ad-panel--chart">
        <h2><span class="vs-dot"></span> Penjualan 7 hari terakhir</h2>
        <div class="ad-chart">
            <canvas id="salesChart" aria-label="Grafik penjualan 7 hari terakhir" role="img"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const canvas = document.getElementById('salesChart');
            if (!canvas || typeof Chart === 'undefined') return;

            let chart = null;

            const css = (name, fallback) => {
                const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
                return value || fallback;
            };

            const toRgba = (color, alpha) => {
                const value = (color || '').trim();
                if (value.startsWith('#') && (value.length === 7 || value.length === 4)) {
                    const hex = value.length === 4
                        ? '#' + [...value.slice(1)].map((c) => c + c).join('')
                        : value;
                    const n = parseInt(hex.slice(1), 16);
                    return `rgba(${(n >> 16) & 255}, ${(n >> 8) & 255}, ${n & 255}, ${alpha})`;
                }
                return value || `rgba(120, 120, 120, ${alpha})`;
            };

            const labels = @json($chartLabels);
            const values = @json($chartValues);

            function render() {
                if (chart) chart.destroy();

                const accent = css('--accent', '#3d2bff');
                const accent2 = css('--accent-2', '#00afce');
                const tick = css('--ink-mute', '#71717c');
                const grid = css('--line', 'rgba(120,120,120,.2)');
                const surface = css('--surface', '#ffffff');
                const ink = css('--ink', '#0e0e12');

                const ctx = canvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, toRgba(accent, 0.28));
                gradient.addColorStop(1, toRgba(accent, 0));

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Penjualan',
                            data: values,
                            borderColor: accent,
                            backgroundColor: gradient,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: accent2,
                            pointBorderColor: surface,
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: ink,
                                titleColor: surface,
                                bodyColor: accent2,
                                borderColor: toRgba(accent, 0.4),
                                borderWidth: 1,
                                padding: 12,
                                boxPadding: 6,
                                usePointStyle: true,
                                callbacks: {
                                    label: (context) => ' Penjualan: Rp ' + (context.parsed.y || 0).toLocaleString('id-ID'),
                                },
                            },
                        },
                        scales: {
                            x: {
                                grid: { color: grid, drawBorder: false },
                                ticks: { color: tick, font: { family: "'Space Grotesk', sans-serif", size: 11 } },
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: grid, drawBorder: false },
                                ticks: {
                                    color: tick,
                                    font: { family: "'Space Grotesk', sans-serif", size: 11 },
                                    callback: (value) => 'Rp ' + value.toLocaleString('id-ID'),
                                },
                            },
                        },
                    },
                });
            }

            render();

            new MutationObserver(render).observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-theme'],
            });
        })();
    </script>
@endpush
