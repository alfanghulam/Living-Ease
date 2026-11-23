@extends('admin.layout')

@section('title', 'Edit Produk')
@section('crumb', 'Katalog / Produk / Edit')

@section('content')
    <div class="ad-head">
        <div>
            <h1>Edit <em>produk</em></h1>
            <p>Perbarui “{{ $product->title }}” — info, harga, stok, atau foto.</p>
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

    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="ad-form" data-atabs>
        @csrf
        @method('PUT')

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
                    <input id="title" class="vs-input" type="text" name="title"
                           value="{{ old('title', $product->title) }}" required>
                </div>

                <div class="vs-field">
                    <label for="category_id">Kategori</label>
                    <select id="category_id" class="vs-input" name="category_id" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                @selected(old('category_id', $product->category_id) == $category->id)>
                                {{ $category->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="vs-field">
                    <label for="brand_id">Brand</label>
                    <select id="brand_id" class="vs-input" name="brand_id" required>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}"
                                @selected(old('brand_id', $product->brand_id) == $brand->id)>
                                {{ $brand->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="vs-field ad-form__full">
                    <label for="keywords">Kata kunci pencarian</label>
                    <input id="keywords" class="vs-input" type="text" name="keywords"
                           value="{{ old('keywords', $product->keywords) }}">
                </div>

                <div class="vs-field ad-form__full">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" class="vs-input" name="description" rows="4" required>{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="ad-panel ad-tabpane" data-apane="pricing" hidden>
            <h2>Harga &amp; stok</h2>

            <div class="ad-form__grid">
                <div class="vs-field">
                    <label for="price">Harga normal (Rp)</label>
                    <input id="price" class="vs-input" type="number" name="price"
                           value="{{ old('price', $product->price) }}" required min="0">
                </div>

                <div class="vs-field">
                    <label for="discount_price">Harga promo (opsional)</label>
                    <input id="discount_price" class="vs-input" type="number" name="discount_price"
                           value="{{ old('discount_price', $product->discount_price) }}" min="0"
                           placeholder="Kosongkan kalau tidak diskon">
                </div>

                <div class="vs-field">
                    <label for="stock">Stok</label>
                    <input id="stock" class="vs-input" type="number" name="stock"
                           value="{{ old('stock', $product->stock) }}" required min="0">
                </div>

                <div class="vs-field">
                    <label for="sku">SKU (opsional)</label>
                    <input id="sku" class="vs-input" type="text" name="sku"
                           value="{{ old('sku', $product->sku) }}">
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
                           value="{{ old('weight_grams', $product->weight_grams) }}" placeholder="mis. 12000 untuk 12 kg">
                </div>

                <div class="vs-field">
                    <label for="length_cm">Panjang (cm)</label>
                    <input id="length_cm" class="vs-input" type="number" name="length_cm" min="1" max="1000"
                           value="{{ old('length_cm', $product->length_cm) }}">
                </div>

                <div class="vs-field">
                    <label for="width_cm">Lebar (cm)</label>
                    <input id="width_cm" class="vs-input" type="number" name="width_cm" min="1" max="1000"
                           value="{{ old('width_cm', $product->width_cm) }}">
                </div>

                <div class="vs-field">
                    <label for="height_cm">Tinggi (cm)</label>
                    <input id="height_cm" class="vs-input" type="number" name="height_cm" min="1" max="1000"
                           value="{{ old('height_cm', $product->height_cm) }}">
                </div>
            </div>

            <p class="ad-hint">
                Isi ukuran dan berat <u>kemasan</u> (setelah dibungkus), dipakai untuk menghitung ongkir.
                Boleh dikosongkan dulu, tapi produk tanpa data ini nanti tidak bisa dihitung ongkirnya otomatis.
            </p>
        </div>

        <div class="ad-panel ad-tabpane" data-apane="photo" data-image-upload data-existing="{{ $product->image_url }}" hidden>
            <h2>Foto produk</h2>

            <div class="ai-box">
                <div class="ai-preview" data-preview>
                    <span class="ai-preview__empty">Belum ada gambar</span>
                </div>

                <div class="ai-side">
                    <label class="ai-drop">
                        <span class="ai-drop__title">Ganti foto (opsional)</span>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" data-input>
                        <span class="ai-drop__hint">Kosongkan kalau tidak ingin mengganti gambar.</span>
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

                    <div class="ai-status" data-status>Pilih foto baru untuk mengaktifkan AI.</div>

                    <button type="button" class="ai-retry" data-retry hidden>Proses ulang</button>
                </div>
            </div>
        </div>

        <div class="ad-form__actions">
            <button type="submit" class="vs-btn vs-btn--primary">Simpan perubahan</button>
            <a href="{{ route('admin.products.index') }}" class="vs-btn vs-btn--line">Batal</a>
        </div>
    </form>

    @if (config('model3d.enabled'))
        <section class="ad-panel ad-model" id="model3d" style="margin-top:18px"
                 data-status-url="{{ route('admin.products.model.status', $product->id) }}"
                 data-status="{{ $product->model_status }}">
            <h2>Model 3D (putar 360°)</h2>

            @if (session('success') || session('error'))
                <p class="ad-hint">{{ session('success') ?? session('error') }}</p>
            @endif

            @if (in_array($product->model_status, ['queued', 'processing'], true))
                <p class="ai-status" data-tone="work" data-model-state>
                    Model 3D sedang dibuat ({{ $product->model_status === 'queued' ? 'menunggu antrean' : 'diproses oleh Hugging Face' }}).
                    Biasanya 1 sampai 3 menit. Halaman ini memperbarui diri otomatis.
                </p>
            @elseif ($product->hasModel())
                <p class="ai-status" data-tone="ok">Model 3D aktif. Pembeli bisa memutarnya di halaman produk.</p>
                <model-viewer src="{{ $product->model_url }}" camera-controls disable-zoom environment-image="neutral"
                              style="width:100%;max-width:420px;height:300px;background:var(--surface-2);border-radius:12px"></model-viewer>
                <script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.0.0/dist/model-viewer.min.js"></script>
            @elseif ($product->model_status === 'failed')
                <p class="ai-status" data-tone="err">Gagal membuat model 3D: {{ $product->model_error ?: 'alasan tidak diketahui' }}</p>
            @else
                <p class="ad-hint">Belum ada model 3D. Halaman produk memakai foto biasa.</p>
            @endif

            <div class="ad-form__actions" style="margin-top:14px">
                <form method="POST" action="{{ route('admin.products.model.generate', $product->id) }}">
                    @csrf
                    <button type="submit" class="vs-btn vs-btn--primary"
                            @disabled(in_array($product->model_status, ['queued', 'processing'], true))>
                        {{ $product->hasModel() ? 'Buat ulang dari foto' : 'Buat model 3D dari foto' }}
                    </button>
                </form>

                @if ($product->model_path)
                    <form method="POST" action="{{ route('admin.products.model.destroy', $product->id) }}"
                          onsubmit="return confirm('Hapus model 3D produk ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="vs-btn vs-btn--line">Hapus model</button>
                    </form>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.products.model.upload', $product->id) }}" enctype="multipart/form-data"
                  class="vs-field" style="margin-top:18px">
                @csrf
                <label for="model_file">Atau unggah file .glb sendiri (maks 20 MB)</label>
                <div class="ad-form__actions">
                    <input id="model_file" class="vs-input" type="file" name="model" accept=".glb" required>
                    <button type="submit" class="vs-btn vs-btn--line">Unggah</button>
                </div>
                @error('model') <span class="vs-errors">{{ $message }}</span> @enderror
            </form>

            <p class="ad-hint">
                Pembuatan berjalan di latar belakang lewat antrean. Pastikan <code>php artisan queue:work</code> aktif
                (atau QUEUE_CONNECTION=sync saat uji coba).
            </p>
        </section>
    @endif
@endsection

@push('scripts')
    @vite('resources/js/admin-product-form.js')
    <script>
        (function () {
            const box = document.getElementById('model3d');
            if (!box || !['queued', 'processing'].includes(box.dataset.status)) return;
            const timer = setInterval(async function () {
                try {
                    const res = await fetch(box.dataset.statusUrl, { headers: { Accept: 'application/json' } });
                    const data = await res.json();
                    if (data.status === 'ready' || data.status === 'failed') { clearInterval(timer); location.reload(); }
                } catch (e) { /* coba lagi pada putaran berikutnya */ }
            }, 4000);
        })();
    </script>
@endpush