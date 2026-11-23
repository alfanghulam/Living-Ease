@extends('admin.layout')

@section('title', 'Percakapan')
@section('crumb', 'Komunikasi / Chat / Percakapan')

@section('content')
    <div class="ad-head">
        <div>
            <h1>Percakapan</h1>
            <p>
                {{ $user ? $user->name.' · '.$user->email : 'Tamu (belum login)' }}
                · sesi #{{ \Illuminate\Support\Str::substr($session, 0, 8) }}
            </p>
        </div>
        <a href="{{ route('admin.chat.index') }}" class="vs-btn vs-btn--line">← Kembali</a>
    </div>

    <div class="ad-panel">
        <h2>Riwayat pesan</h2>

        <div class="ad-thread">
            @foreach ($messages as $message)
                @php
                    $isCustomer = $message->role === 'user';
                    $isAdmin = ! $isCustomer && (bool) data_get($message->meta, 'admin');
                @endphp

                <div @class([
                    'ad-msg',
                    'ad-msg--user' => $isCustomer,
                    'ad-msg--admin' => $isAdmin,
                    'ad-msg--bot' => ! $isCustomer && ! $isAdmin,
                ])>
                    <span class="ad-msg__meta">
                        {{ $isCustomer ? 'Pelanggan' : ($isAdmin ? 'Admin' : 'Asisten AI') }}
                        · {{ $message->created_at?->format('d/m H:i') }}
                    </span>
                    {{ $message->content }}
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('admin.chat.reply', $session) }}" class="ad-chat-form">
            @csrf
            <textarea class="vs-input" name="content" rows="2" required
                      placeholder="Tulis balasan untuk pelanggan..."></textarea>
            <button type="submit" class="vs-btn vs-btn--primary">Kirim</button>
        </form>
    </div>
@endsection
