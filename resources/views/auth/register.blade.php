@extends('layouts.app')

@section('title', 'Daftar Akun — LivingEase')

@section('content')
    <div class="vs-shell" style="padding-top: 40px; padding-bottom: 60px;">
        <div style="max-width: 440px; margin: 0 auto;">
            <div class="ad-panel" style="padding: 30px 28px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px;">
                    <span class="ad-auth-badge">
                        <span class="vs-dot"></span>
                        REGISTRASI BARU
                    </span>
                    <span class="ad-crumb">LIVINGEASE</span>
                </div>

                <h1 style="font-family: var(--font-display); font-size: 24px; font-weight: 700; letter-spacing: -0.6px; margin: 0 0 6px;">Buat Akun</h1>
                <p style="font-size: 13px; color: var(--ink-soft); margin: 0 0 20px; line-height: 1.5;">Daftar gratis untuk proses checkout lebih cepat dan melacak pesananmu.</p>

                @if ($errors->any())
                    <div class="vs-alert vs-alert--err">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" style="display: grid; gap: 14px;">
                    @csrf

                    <div>
                        <label for="name" style="display: block; font-family: var(--font-display); font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--ink-soft); margin-bottom: 6px;">Nama Lengkap</label>
                        <input id="name" class="vs-input" type="text" name="name" value="{{ old('name') }}"
                               required autofocus autocomplete="name" placeholder="Nama kamu">
                    </div>

                    <div>
                        <label for="email" style="display: block; font-family: var(--font-display); font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--ink-soft); margin-bottom: 6px;">Email</label>
                        <input id="email" class="vs-input" type="email" name="email" value="{{ old('email') }}"
                               required autocomplete="username" placeholder="nama@example.com">
                    </div>

                    <div>
                        <label for="password" style="display: block; font-family: var(--font-display); font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--ink-soft); margin-bottom: 6px;">Password</label>
                        <input id="password" class="vs-input" type="password" name="password"
                               required autocomplete="new-password" placeholder="Minimal 8 karakter">
                    </div>

                    <div>
                        <label for="password_confirmation" style="display: block; font-family: var(--font-display); font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--ink-soft); margin-bottom: 6px;">Ulangi Password</label>
                        <input id="password_confirmation" class="vs-input" type="password"
                               name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                    </div>

                    <button type="submit" class="vs-btn vs-btn--primary vs-btn--block" style="margin-top: 6px;">Daftar Akun Sekarang</button>
                </form>

                <div style="margin-top: 20px; padding-top: 16px; border-top: 1px dashed var(--line); text-align: center; font-size: 12.5px; color: var(--ink-soft);">
                    Sudah punya akun? <a href="{{ route('login') }}" style="color: var(--ink); font-weight: 600; text-decoration: underline;">Masuk di sini</a>
                </div>
            </div>
        </div>
    </div>
@endsection
