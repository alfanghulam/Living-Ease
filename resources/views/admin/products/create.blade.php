@extends('admin.layout')

@section('title', 'Tambah Produk')
@section('crumb', 'Katalog / Produk / Tambah')

@section('content')
    <div class="ad-head">
        <div>
            <h1>Tambah <em>produk</em></h1>
            
        </div>
        <a href="{{ route('admin.products.index') }}" class="vs-btn vs-btn--line">← Kembali</a>
    </div>

    @if ($errors->any())
        <div class="vs-errors">
            @foreach ($errors->all() as $error)
                <span>{{ $error }}</span>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="ad-form" data-atabs>
        @csrf

        <div class="ad-formtabs">
            <button type="button" class="ad-formtab is-on" data-atab="info">Info</button>
            <button type="button" class="ad-formtab" data-atab="pricing">Harga &amp; Stok</button>
            <button type="button" class="ad-formtab" data-atab="shipping">Pengiriman</button>
            <button type="button" class="ad-formtab" data-atab="photo">Foto</button>
        </div>

        <div class="ad-panel ad-tabpane" data-apane="info">
            <h2>Informasi produk</h2>

            <div class="ad-form__grid">
                <div class="vs-field ad-form__full">
                    <label for="title">Nama produk</label>
                    <input id="title" class="vs-input" type="text" name="title" value="{{ old('title') }}"
                           required placeholder="Contoh: Laptop Pavillion">
                </div>

                <div class="vs-field">
                    <label for="category_id">Kategori</label>
                    <select id="category_id" class="vs-input" name="category_id" required>
                        <option value="">— Pilih kategori —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                {{ $category->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="vs-field">
                    <label for="brand_id">Brand</label>
                    <select id="brand_id" class="vs-input" name="brand_id" required>
                        <option value="">— Pilih brand —</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>
                                {{ $brand->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="vs-field ad-form__full">
                    <label for="keywords">Kata kunci pencarian</label>
                    <input id="keywords" class="vs-input" type="text" name="keywords" value="{{ old('keywords') }}"
                           placeholder="laptop hp pavillion">
                </div>

                <div class="vs-field ad-form__full">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" class="vs-input" name="description" rows="4" required
                              placeholder="Tulis deskripsi singkat produk...">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="ad-panel ad-tabpane" data-apane="pricing" hidden>
            <h2>Harga &amp; stok</h2>

            <div class="ad-form__grid">
                <div class="vs-field">
                    <label for="price">Harga normal (Rp)</label>
                    <input id="price" class="vs-input" type="number" name="price" value="{{ old('price') }}"
                           required min="0" placeholder="150000">
                </div>

                <div class="vs-field">
                    <label for="discount_price">Harga promo (opsional)</label>
                    <input id="discount_price" class="vs-input" type="number" name="discount_price"
                           value="{{ old('discount_price') }}" min="0" placeholder="Kosongkan kalau tidak diskon">
                </div>

                <div class="vs-field">
                    <label for="stock">Stok</label>
                    <input id="stock" class="vs-input" type="number" name="stock" value="{{ old('stock', 0) }}"
                           required min="0" placeholder="0">
                </div>

                <div class="vs-field">
                    <label for="sku">SKU (opsional)</label>
                    <input id="sku" class="vs-input" type="text" name="sku" value="{{ old('sku') }}"
                           placeholder="VS-0010">
                </div>
            </div>

            <p class="ad-hint">
                Harga promo harus lebih kecil dari harga normal. Kalau diisi, toko otomatis menampilkan harga coret.
            </p>
        </div>

        <div class="ad-panel ad-tabpane" data-apane="shipping" hidden>
            <h2>Data pengiriman</h2>

            <div class="ad-form__grid">
                <div class="vs-field">
                    <label for="weight_grams">Berat kemasan (gram)</label>
                    <input id="weight_grams" class="vs-input" type="number" name="weight_grams" min="1" max="500000"
                           value="{{ old('weight_grams') }}" placeholder="mis. 12000 untuk 12 kg">
                </div>

                <div class="vs-field">
                    <label for="length_cm">Panjang (cm)</label>
                    <input id="length_cm" class="vs-input" type="number" name="length_cm" min="1" max="1000"
                           value="{{ old('length_cm') }}">
                </div>

                <div class="vs-field">
                    <label for="width_cm">Lebar (cm)</label>
                    <input id="width_cm" class="vs-input" type="number" name="width_cm" min="1" max="1000"
                           value="{{ old('width_cm') }}">
                </div>

                <div class="vs-field">
                    <label for="height_cm">Tinggi (cm)</label>
                    <input id="height_cm" class="vs-input" type="number" name="height_cm" min="1" max="1000"
                           value="{{ old('height_cm') }}">
                </div>
            </div>

            <p class="ad-hint">
                Isi ukuran dan berat <u>kemasan</u> (setelah dibungkus), dipakai untuk menghitung ongkir.
                Boleh dikosongkan dulu, tapi produk tanpa data ini nanti tidak bisa dihitung ongkirnya otomatis.
            </p>
        </div>

        <div class="ad-panel ad-tabpane" data-apane="photo" data-image-upload hidden>
            <h2>Foto produk</h2>

            <div class="ai-box">
                <div class="ai-preview" data-preview>
                    <span class="ai-preview__empty">Belum ada gambar</span>
                </div>

                <div class="ai-side">
                    <label class="ai-drop">
                        <span class="ai-drop__title">Pilih foto produk</span>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required data-input>
                        <span class="ai-drop__hint">JPG / PNG / WebP · maks 4 MB</span>
                    </label>

                    <label class="ai-toggle">
                        <input type="checkbox" data-toggle checked>
                        <span>Hapus background otomatis (AI)</span>
                    </label>

                    @if (config('model3d.enabled'))
                        <label class="ai-toggle">
                            <input type="checkbox" name="generate_3d" value="1" @checked(old('generate_3d'))>
                            <span>Buat model 3D otomatis dari foto ini (bisa diputar 360° di halaman produk)</span>
                        </label>
                    @endif

                    <div class="ai-status" data-status>Foto asli akan dipakai apa adanya.</div>

                    <button type="button" class="ai-retry" data-retry hidden>Proses ulang</button>
                </div>
            </div>
        </div>

        <div class="ad-form__actions">
            <button type="submit" class="vs-btn vs-btn--primary">Simpan produk</button>
            <a href="{{ route('admin.products.index') }}" class="vs-btn vs-btn--line">Batal</a>
        </div>
    </form>
@endsection

@push('scripts')
    @vite('resources/js/admin-product-form.js')
@endpush