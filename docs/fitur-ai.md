# Fitur AI & Tema — Panduan Singkat

Dokumen ini menjelaskan tiga fitur yang ditambahkan di atas kode lama: **tema gelap/terang**, **chatbot AI**, dan **hapus background otomatis** untuk foto produk.

---

## 1. Tema gelap / terang

- Tombol toggle ada di navbar storefront **dan** di panel admin (ikon matahari/bulan).
- Default mengikuti setelan OS pengunjung (`prefers-color-scheme`), lalu pilihan disimpan di `localStorage` dengan key `vanz-theme`.
- Storefront dan admin berbagi key yang sama, jadi sekali pilih tema, keduanya konsisten.
- Admin memakai layout sendiri (`resources/views/admin/layout.blade.php`) tetapi membaca token yang sama dari `app.css` + `admin.css`.
- Semua warna berasal dari token di `resources/css/app.css`:

| Token | Terang | Gelap |
| --- | --- | --- |
| `--bg` / `--surface` | `#ffffff` / `#ffffff` | `#07040f` / `#151029` |
| `--ink` | `#0e0e12` | `#f4f1ff` |
| `--accent` | `#3d2bff` (indigo) | `#c6ff3d` (lime) |
| `--accent-2` | `#00afce` | `#35f0ff` |

Menambah tema baru = menambah satu blok `:root[data-theme='...']`, tanpa menduplikasi markup.

Agar tidak ada kedipan saat halaman dimuat, ada script kecil di `<head>` (`layouts/app.blade.php`) yang memasang `data-theme` sebelum CSS dilukis.

---

## 2. Chatbot AI

### Cara mengaktifkan

Isi tiga variabel di `.env`:

```env
CHATBOT_API_KEY=sk-xxxxxxxx
CHATBOT_BASE_URL=https://api.deepseek.com/v1
CHATBOT_MODEL=deepseek-chat
```

Endpoint apa pun yang kompatibel OpenAI bisa dipakai — cukup ganti base URL dan model:

| Provider | Base URL | Contoh model |
| --- | --- | --- |
| DeepSeek | `https://api.deepseek.com/v1` | `deepseek-chat` |
| OpenAI | `https://api.openai.com/v1` | `gpt-4o-mini` |
| Groq | `https://api.groq.com/openai/v1` | `llama-3.3-70b-versatile` |
| OpenRouter | `https://openrouter.ai/api/v1` | `openai/gpt-4o-mini` |

Kalau `CHATBOT_API_KEY` kosong, widget tetap tampil dengan pesan fallback + nomor WhatsApp — jadi toko tidak rusak sebelum key diisi.

### Yang dikirim ke model

`ChatbotService` menyusun system prompt berisi:

- gaya bicara (santai, singkat, bahasa Indonesia),
- info toko (ongkir, COD, garansi, jam kirim, kode promo),
- daftar katalog aktif (nama | harga | kategori, di-cache 10 menit),
- aturan: tidak mengarang produk/harga/kebijakan, arahkan ke admin untuk stok pasti atau komplain.

Riwayat percakapan diambil maksimal `CHATBOT_HISTORY_LIMIT` pesan terakhir supaya konteks tetap murah.

### Penyimpanan & keamanan

- Tabel `chat_messages` (`session_id`, `user_id`, `role`, `content`, `meta`).
- Session id disimpan di session Laravel (`chat_session`), jadi tamu pun bisa chat.
- Rate limit: 20 pesan/menit per IP; panjang pesan maks 500 karakter.
- Kunci API hanya dipakai di server, tidak pernah dikirim ke browser.

### Melihat percakapan

```bash
php artisan tinker
>>> App\Models\ChatMessage::latest()->take(10)->get(['session_id','role','content']);
```

---

## 3. Hapus background otomatis (admin)

- Library: `@imgly/background-removal` (model ONNX, jalan di browser via WASM).
- Dipakai di form **Tambah Produk** dan **Edit Produk** (`resources/js/admin-product-form.js`).
- Alur: pilih foto → foto diperkecil ke maks 1200px → model dijalankan → hasil **WebP transparan** menggantikan file di input → preview ditampilkan di atas kotak-kotak.

Catatan penting:

1. **Foto tidak dikirim ke server mana pun** saat diproses; yang di-upload hanya hasil akhirnya.
2. Model diunduh sekali dari CDN saat admin pertama kali memakai fitur (± 40 MB), lalu di-cache browser. Butuh koneksi internet pada pemakaian pertama.
3. Kalau proses gagal (offline, browser lama, foto tidak didukung), file asli tetap dipakai — ada tombol **Proses ulang**.
4. Toggle "Hapus background otomatis (AI)" bisa dimatikan kalau mau upload foto apa adanya.
5. Batas upload admin dinaikkan ke 4 MB (`max:4096`).

---

## Perintah harian

```bash
composer install
npm install
php artisan migrate
php artisan storage:link   # wajib: tanpa ini foto upload (avatar & produk) 404
npm run dev      # atau: npm run build
php artisan serve
```

Untuk produksi: `npm run build` lalu arahkan document root ke `public/`.

## Panel admin

- URL: `/admin/login` (juga ada link kecil **Admin** di footer storefront, pojok kiri bawah).
- Akun demo: `admin@gmail.com` / `admin123`.
- Halaman: Dashboard (statistik + grafik penjualan 7 hari), Daftar Produk, Tambah Produk, Edit Produk.
- Semua halaman admin memakai tema yang sama dengan storefront dan ikut tombol gelap/terang.
- Grafik memakai Chart.js (CDN) dan warnanya dibaca dari token CSS, jadi ikut berubah saat tema diganti.
