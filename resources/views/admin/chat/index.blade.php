@extends('admin.layout')

@section('title', 'Chat')
@section('crumb', 'Komunikasi / Chat')

@section('content')
    <div class="ad-head">
        <div>
            <h1>Inbox <em>chat</em></h1>
            <p>{{ $totalSessions }} percakapan · balas manual kapan pun kamu mau.</p>
        </div>
    </div>

    @if ($sessions->isEmpty())
        <div class="vs-empty">
            <h3>Belum ada percakapan</h3>
            <p>Percakapan dari widget chat di toko akan muncul di sini.</p>
        </div>
    @else
        <div class="ad-table-wrap">
            <table class="ad-table">
                <thead>
                    <tr>
                        <th>Percakapan</th>
                        <th>Pengguna</th>
                        <th>Pesan</th>
                        <th>Terakhir</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessions as $session)
                        @php
                            $last = $lastMessages[$session->last_id] ?? null;
                            $needsReply = $last && $last->role === 'user';
                            $userId = $sessionUsers[$session->session_id] ?? null;
                            $chatter = $userId ? ($users[$userId] ?? null) : null;
                        @endphp
                        <tr>
                            <td>
                                <span class="ad-name">#{{ \Illuminate\Support\Str::substr($session->session_id, 0, 8) }}</span>
                                <div class="ad-muted">mulai {{ \Illuminate\Support\Carbon::parse($session->started_at)->format('d M H:i') }}</div>
                            </td>
                            <td>
                                @if ($chatter)
                                    <div class="ad-name">{{ $chatter->name }}</div>
                                    <div class="ad-muted">{{ $chatter->email }}</div>
                                @else
                                    <span class="ad-muted">Tamu (belum login)</span>
                                @endif
                            </td>
                            <td class="ad-muted">{{ $session->total }} pesan</td>
                            <td class="ad-muted">
                                {{ $last ? \Illuminate\Support\Str::limit($last->content, 48) : '—' }}
                            </td>
                            <td>
                                @if ($needsReply)
                                    <span class="vs-pill vs-pill--warn">Perlu balas</span>
                                @else
                                    <span class="vs-pill vs-pill--ok">Sudah dibalas</span>
                                @endif
                            </td>
                            <td>
                                <div class="ad-actions">
                                    <a class="ad-btn" href="{{ route('admin.chat.show', $session->session_id) }}">Buka</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="ad-pager">
            <span>Menampilkan {{ $sessions->firstItem() }}–{{ $sessions->lastItem() }} dari {{ $sessions->total() }} percakapan</span>
            <div class="ad-pager__links">
                @if (! $sessions->onFirstPage())
                    <a class="ad-btn" href="{{ $sessions->previousPageUrl() }}">← Sebelumnya</a>
                @endif
                <span class="ad-muted">Hal {{ $sessions->currentPage() }} / {{ $sessions->lastPage() }}</span>
                @if ($sessions->hasMorePages())
                    <a class="ad-btn" href="{{ $sessions->nextPageUrl() }}">Berikutnya →</a>
                @endif
            </div>
        </div>
    @endif
@endsection
