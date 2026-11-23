@extends('admin.layout')

@section('title', $title)
@section('crumb', 'Katalog / '.$title)

@section('content')
    <div class="ad-head">
        <div>
            <h1>{{ $title }}</h1>
            <p>{{ $items->count() }} {{ $singular }} terdaftar · kelola pengelompokan katalog.</p>
        </div>
    </div>

    <div class="ad-split">
        <div class="ad-panel">
            <h2>Daftar {{ $singular }}</h2>

            @if ($items->isEmpty())
                <p class="ad-hint">Belum ada {{ $singular }}. Tambahkan lewat form di samping.</p>
            @else
                <div class="ad-table-wrap ad-table-wrap--flush">
                    <table class="ad-table">
                        <thead>
                            <tr>
                                <th>{{ ucfirst($singular) }}</th>
                                <th>{{ $productsLabel }}</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td><span class="ad-name">{{ $item->title }}</span></td>
                                    <td>
                                        @if ($item->products_count > 0)
                                            <span class="vs-pill">{{ $item->products_count }} produk</span>
                                        @else
                                            <span class="ad-muted">kosong</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="ad-actions">
                                            <a class="ad-btn ad-btn--warn"
                                               href="{{ route($routeBase.'.index', ['edit' => $item->id]) }}">Edit</a>

                                            <form method="POST" action="{{ route($routeBase.'.destroy', $item) }}"
                                                  onsubmit="return confirm('Hapus {{ $singular }} ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ad-btn ad-btn--danger">Hapus</button>
                                            </form>
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
            <h2>{{ $editing ? 'Edit '.$singular : 'Tambah '.$singular }}</h2>

            <form method="POST"
                  action="{{ $editing ? route($routeBase.'.update', $editing) : route($routeBase.'.store') }}">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                <div class="vs-field">
                    <label for="title">Nama {{ $singular }}</label>
                    <input id="title" class="vs-input" type="text" name="title"
                           value="{{ old('title', $editing?->title) }}" required
                           placeholder="Misal: {{ $singular === 'brand' ? 'Samsung' : 'Elektronik' }}">
                </div>

                @if ($errors->any())
                    <div class="vs-errors">
                        @foreach ($errors->all() as $error)
                            <span>{{ $error }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="ad-form__actions">
                    <button type="submit" class="vs-btn vs-btn--primary">
                        {{ $editing ? 'Simpan perubahan' : 'Tambah' }}
                    </button>
                    @if ($editing)
                        <a href="{{ route($routeBase.'.index') }}" class="vs-btn vs-btn--line">Batal</a>
                    @endif
                </div>
            </form>

            <p class="ad-hint">
                {{ ucfirst($singular) }} yang masih dipakai produk tidak bisa dihapus — pindahkan produknya dulu
                supaya katalog tidak ikut terhapus.
            </p>
        </div>
    </div>
@endsection
