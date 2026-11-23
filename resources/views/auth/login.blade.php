@extends('layouts.app')

@section('title', 'Masuk Akun — LivingEase')

@section('content')
    <div class="vs-shell" style="padding-top: 40px; padding-bottom: 60px;">
        <div style="max-width: 420px; margin: 0 auto;">
            <div class="ad-panel" style="padding: 30px 28px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px;">
                    <span class="ad-auth-badge">
                        <span class="vs-dot"></span>
                        LOGIN PELANGGAN
                    </span>
                    <span class="ad-crumb">LIVINGEASE</span>
                </div>

                <h1 style="font-family: var(--font-display); font-size: 24px; font-weight: 700; letter-spacing: -0.6px; margin: 0 0 6px;">Masuk Akun</h1>
                <p style="font-size: 13px; color: var(--ink-soft); margin: 0 0 20px; line-height: 1.5;">Lanjutkan belanja, cek keranjang, dan pantau status pesananmu.</p>

                @if ($errors->any())
                    <div class="vs-alert vs-alert--err">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" style="display: grid; gap: 14px;">
                    @csrf

                    <div>
                        <label for="email" style="display: block; font-family: var(--font-display); font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--ink-soft); margin-bottom: 6px;">Email</label>
                        <input id="email" class="vs-input" type="email" name="email" value="{{ old('email') }}"
                               required autofocus autocomplete="username" placeholder="nama@example.com">
                    </div>

                    <div>
                        <label for="password" style="display: block; font-family: var(--font-display); font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--ink-soft); margin-bottom: 6px;">Password</label>
                        <input id="password" class="vs-input" type="password" name="password"
                               required autocomplete="current-password" placeholder="••••••••">
                    </div>

                    <button type="submit" class="vs-btn vs-btn--primary vs-btn--block" style="margin-top: 6px;">Masuk ke Akun</button>
                </form>

                <div style="margin-top: 20px; padding-top: 16px; border-top: 1px dashed var(--line); text-align: center; font-size: 12.5px; color: var(--ink-soft);">
                    Belum punya akun? <a href="{{ route('register') }}" style="color: var(--ink); font-weight: 600; text-decoration: underline;">Daftar sekarang</a>
                </div>

                @if (app()->environment('local'))
                    <div style="margin-top: 14px; padding: 10px 12px; border-radius: 8px; background: var(--surface-2); border: 1px solid var(--line); font-size: 11.5px; color: var(--ink-soft);">
                        <b>Akun demo:</b> user@gmail.com / user12345
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
