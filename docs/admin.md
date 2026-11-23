# Panel Admin — Panduan Modul

Login: `/admin/login` (akun demo `admin@gmail.com` / `admin123`). Link **Admin** juga ada di footer storefront, pojok kiri bawah.

Semua halaman memakai token warna yang sama dengan storefront, jadi ikut tombol gelap/terang.

## Struktur menu

```
OVERVIEW      Dashboard
KATALOG       Produk · Kategori · Brand
PENJUALAN     Pesanan · Pelanggan
KOMUNIKASI    Chat
SISTEM        Pengaturan
```

## Dashboard

- KPI: total penjualan, total pesanan, pesanan 7 hari, total user.
- **Perlu tindakan**: pesanan menunggu bayar, produk stok ≤ 3, pesanan 7 hari (bisa diklik).
- Tabel pesanan terbaru + grafik penjualan 7 hari (Chart.js, warna mengikuti tema).

## Pesanan

- Tab status (Semua / Menunggu bayar / Dibayar / Dikirim / Selesai / Dibatalkan) dengan jumlah.
- Pencarian: nomor pesanan, nama, email, nomor resi. Pagination 12/halaman.
- Detail: item + snapshot harga, alamat kirim, ringkasan, catatan internal, timeline.
- **Alur status** (tidak bisa melompat):

```text
pending ──► paid ──► shipped ──► completed
   │          │
   └──────────┴──► cancelled
```

- Saat status menjadi **Dibayar**, stok tiap produk dikurangi. Jika dibatalkan dari status dibayar, stok dikembalikan.
- Nomor resi & catatan internal bertimestamp disimpan di pesanan.
- **Export CSV** mengikuti filter yang aktif (BOM UTF-8 supaya rapi di Excel).

## Kategori & Brand

- CRUD di satu halaman: daftar di kiri, form tambah/edit di kanan.
- Kategori/brand **tidak bisa dihapus** selama masih dipakai produk (mencegah produk ikut terhapus karena foreign key cascade).
- Jumlah produk per kategori/brand ditampilkan di daftar.

## Produk

Form dibagi menjadi tiga tab: **Info · Harga & Stok · Foto**.

| Field | Catatan |
| --- | --- |
| SKU | opsional, unik |
| Harga normal | wajib |
| Harga promo | opsional, harus lebih kecil dari harga normal |
| Stok | wajib, dipakai untuk cek keranjang/checkout |
| Foto | upload + hapus background AI (WebP transparan) |

Kalau harga promo diisi, storefront otomatis menampilkan harga promo + harga coret dan badge “HEMAT n%”, dan transaksi (cart, checkout, order) memakai harga promo.

## Aturan stok

- Keranjang menolak jumlah melebihi stok (`422` untuk request AJAX, pesan error untuk form biasa).
- Checkout menolak pesanan yang stoknya tidak cukup.
- Produk dengan stok 0 tidak bisa dibeli; tombol berubah menjadi “Stok habis”.

## Pelanggan

- Daftar akun non-admin + jumlah pesanan, total belanja, dan tanggal order terakhir.
- Pencarian nama/email, pagination 15.
- Detail pelanggan: kartu statistik (jumlah order, total belanja, order terakhir, rata-rata per order), riwayat pesanan (bisa dibuka ke detail pesanan), dan cuplikan percakapan terakhir.

## Chat (inbox)

- Daftar sesi percakapan dari widget toko: pengunjung (login/tamu), jumlah pesan, cuplikan pesan terakhir.
- Status **Perlu balas** kalau pesan terakhir dari pembeli, **Sudah dibalas** kalau admin/AI yang terakhir membalas.
- Buka sesi → thread lengkap (pelanggan di kiri, AI/admin di kanan) → balas manual. Balasan tersimpan sebagai pesan `assistant` dengan `meta.admin = true`.
- Widget pembeli menarik riwayat tiap 12 detik saat panel terbuka, jadi balasan admin muncul tanpa perlu tutup-buka.

## Pengaturan

Tersimpan di tabel `settings` (key/value, di-cache 1 jam) dan langsung dipakai storefront:

| Setting | Efek |
| --- | --- |
| `store_name`, `store_email` | Identitas toko |
| `store_phone`, `store_address` | Footer storefront |
| `chatbot_whatsapp` | Nomor rujukan chatbot saat mengarahkan ke admin |
| `promo_code`, `promo_percent`, `promo_active` | Blok promo di halaman katalog (bisa disembunyikan) |

Kalau `promo_active` dimatikan, blok promo hilang dari storefront tanpa mengubah kode.
