@extends('layouts.app')

@section('title', 'Profil Saya — LivingEase')

@section('content')
    <div class="vs-shell" style="padding-top: 24px; padding-bottom: 60px;">
        <div class="ad-head">
            <div>
                <span class="ad-crumb" style="display:inline-block; margin-bottom: 6px;">PELANGGAN · AKUN SAYA</span>
                <h1>Profil <em>Saya</em></h1>
                <p>Kelola foto profil dan pantau riwayat serta status pesananmu.</p>
            </div>
            <a href="{{ route('home') }}" class="ad-btn">← Lanjut Belanja</a>
        </div>

        @if (session('success'))
            <div class="vs-alert vs-alert--ok">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="vs-alert vs-alert--err">{{ session('error') }}</div>
        @endif

        <div class="vs-profile" style="display: grid; grid-template-columns: 320px 1fr; gap: 20px; align-items: start;">
            {{-- Kartu Info User --}}
            <div class="ad-panel">
                <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--line);">
                    <div style="width: 54px; height: 54px; border-radius: 12px; overflow: hidden; background: var(--surface-2); border: 1px solid var(--line); flex-shrink: 0; display: grid; place-items: center;">
                        <img src="{{ $user->profile_photo ? \App\Support\Media::url($user->profile_photo) : $avatarPlaceholder }}"
                             alt="Foto profil {{ $user->name }}"
                             style="width: 100%; height: 100%; object-fit: cover;"
                             onerror="this.onerror=null;this.src='{{ $avatarPlaceholder }}'">
                    </div>
                    <div>
                        <div style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--ink);">{{ $user->name }}</div>
                        <div style="font-size: 12px; color: var(--ink-soft);">{{ $user->email }}</div>
                        <div style="font-family: var(--font-display); font-size: 9.5px; letter-spacing: 1px; color: var(--ink-mute); text-transform: uppercase; margin-top: 3px;">
                            Member sejak {{ $user->created_at?->format('d M Y') }}
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('user.photo') }}" enctype="multipart/form-data" style="display: grid; gap: 12px;">
                    @csrf
                    <div>
                        <label for="photo" style="display: block; font-family: var(--font-display); font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; color: var(--ink-mute); margin-bottom: 6px;">
                            Ganti Foto Profil
                        </label>
                        <input id="photo" class="vs-input" type="file" name="photo" accept="image/*" required style="padding: 6px 10px; font-size: 12px;">
                    </div>
                    <button type="submit" class="vs-btn vs-btn--primary vs-btn--block">Simpan Foto</button>
                </form>

                <form method="POST" action="{{ route('logout') }}" style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed var(--line);">
                    @csrf
                    <button type="submit" class="ad-btn" style="width: 100%; justify-content: center; color: var(--danger);">Keluar Akun</button>
                </form>
            </div>

            {{-- Riwayat Pesanan --}}
            <div class="ad-panel">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <h2 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <span class="vs-dot"></span>
                        Riwayat Pesanan
                    </h2>
                    <span class="ad-crumb">{{ $orders->count() }} TOTAL PESANAN</span>
                </div>

                @if ($orders->isEmpty())
                    <div style="text-align: center; padding: 48px 20px; border: 1px dashed var(--line); border-radius: 12px; background: var(--surface-2);">
                        <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; margin: 0 0 6px;">Belum Ada Pesanan</h3>
                        <p style="font-size: 13px; color: var(--ink-soft); margin: 0 0 16px;">Semua pesananmu akan tercatat otomatis di sini setelah checkout.</p>
                        <a href="{{ route('home') }}" class="vs-btn vs-btn--primary">Mulai Belanja Sekarang →</a>
                    </div>
                @else
                    <div style="display: grid; gap: 12px;">
                        @foreach ($orders as $order)
                            <details class="vs-order-card" {{ $loop->first ? 'open' : '' }} style="border: 1px solid var(--line); border-radius: 12px; overflow: hidden; background: var(--surface); transition: .16s;">
                                <summary style="padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; background: var(--surface-2); user-select: none;">
                                    <span style="display: flex; align-items: center; gap: 10px;">
                                        <b style="font-family: var(--font-display); font-size: 14px;">#{{ $order->id }}</b>
                                        <span class="vs-pill vs-pill--{{ $order->statusTone() }}">{{ $order->statusLabel() }}</span>
                                    </span>
                                    <span style="display: flex; align-items: center; gap: 12px;">
                                        <small style="color: var(--ink-mute); font-size: 11px; font-family: var(--font-display);">{{ $order->created_at?->format('d M Y, H:i') }}</small>
                                        <b style="font-family: var(--font-display); font-size: 14px; color: var(--ink);">Rp {{ number_format($order->total, 0, ',', '.') }}</b>
                                    </span>
                                </summary>

                                <div style="padding: 14px 18px; border-top: 1px solid var(--line); display: grid; gap: 10px;">
                                    @foreach ($order->items as $item)
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <span style="width: 44px; height: 44px; border-radius: 8px; background: var(--photo-tile); border: 1px solid var(--line); display: grid; place-items: center; overflow: hidden; flex-shrink: 0;">
                                                <img src="{{ $item->product?->image_url ?? asset('img/no-image.png') }}" alt=""
                                                     style="max-width: 85%; max-height: 85%; object-fit: contain;"
                                                     onerror="this.onerror=null;this.src='{{ asset('img/no-image.png') }}'">
                                            </span>
                                            <div style="flex: 1;">
                                                <div style="font-family: var(--font-display); font-size: 13px; font-weight: 600; color: var(--ink);">
                                                    {{ $item->product?->title ?? 'Produk tidak tersedia' }}
                                                </div>
                                                <div style="font-size: 11.5px; color: var(--ink-soft);">
                                                    {{ $item->qty }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div style="font-family: var(--font-display); font-size: 13px; font-weight: 700; color: var(--ink);">
                                                Rp {{ number_format($item->qty * $item->price, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div style="padding: 12px 18px; border-top: 1px solid var(--line); background: var(--surface-2); display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: var(--ink-soft);">
                                    <span>Dikirim ke <b>{{ $order->city }}</b> ({{ $order->courier ? strtoupper($order->courier).' - '.$order->service_code : 'Kurir Standar' }})</span>
                                    <span>Total Tagihan: <b style="font-family: var(--font-display); font-size: 14px; color: var(--ink);">Rp {{ number_format($order->total, 0, ',', '.') }}</b></span>
                                </div>

                                @if ($order->isAwaitingPayment() && app(\App\Services\MidtransService::class)->isConfigured())
                                    <div style="padding: 12px 18px; border-top: 1px solid var(--line); display: flex; justify-content: flex-end; gap: 8px;">
                                        <a href="{{ route('payment.show', $order->id) }}" class="vs-btn vs-btn--primary">Bayar Sekarang →</a>
                                        <a href="{{ route('checkout.success', $order->id) }}" class="ad-btn">Detail Status</a>
                                    </div>
                                @elseif ($order->status === 'completed')
                                    <div style="padding: 12px 18px; border-top: 1px solid var(--line); display: flex; justify-content: flex-end; gap: 8px; flex-wrap: wrap;">
                                        @foreach ($order->items->filter(fn ($i) => $i->product) as $item)
                                            <a href="{{ route('products.show', $item->product) }}#ulasan" class="ad-btn">Ulas {{ \Illuminate\Support\Str::limit($item->product->title, 20) }}</a>
                                        @endforeach
                                    </div>
                                @endif
                            </details>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
