@extends('layouts.admin-auth')

@section('title', 'Admin Login')

@section('content')
    <div class="ad-auth-card">
        <div class="ad-auth-top">
            <span class="ad-auth-badge">
                <span class="vs-dot"></span>
                Admin console
            </span>

            <button type="button" class="vs-btn-icon vs-theme" data-theme-toggle aria-label="Ganti tema" title="Ganti tema">
                <svg class="vs-ico vs-sun" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>
                </svg>
                <svg class="vs-ico vs-moon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M21 12.8A8.5 8.5 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/>
                </svg>
            </button>
        </div>

        <h1>Masuk admin</h1>
        <p class="ad-auth-sub">Halaman ini khusus pengelola toko. Di sini kamu bisa menambah produk, mengatur harga, dan memantau penjualan.</p>

        @if ($errors->any())
            <div class="vs-errors">
                @foreach ($errors->all() as $error)
                    <span>{{ $error }}</span>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="vs-field">
                <label for="email">Email admin</label>
                <input id="email" class="vs-input" type="email" name="email" value="{{ old('email') }}"
                       required autofocus autocomplete="username" placeholder="admin@example.com">
            </div>

            <div class="vs-field">
                <label for="password">Password</label>
                <input id="password" class="vs-input" type="password" name="password"
                       required autocomplete="current-password" placeholder="••••••••">
            </div>

            <button type="submit" class="vs-btn vs-btn--primary vs-btn--block">Masuk ke panel</button>
        </form>

        <div class="ad-auth-alt">
            <a href="{{ route('welcome') }}">← Kembali ke toko</a>
        </div>
    </div>
@endsection
