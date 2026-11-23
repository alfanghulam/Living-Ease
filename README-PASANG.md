# Vanz Living — proyek Laravel (siap pakai)

Proyek ini sudah berisi: desain klasik (CSS sudah terkompilasi), produk bisa diputar 360° (model 3D),
pembayaran Midtrans, ulasan asli, panel admin, dan chatbot. Dependensi PHP (`vendor`) sudah termasuk.

## Pasang di Laragon (Windows), 5 langkah

1. **Ekstrak** zip ini ke `C:\laragon\www\vanz-living` (atau folder `www` Laragon-mu).
   Pastikan file `artisan` ada langsung di dalam folder itu, bukan di subfolder lagi.
2. **Buat database kosong** bernama `vanz_living`.
   Laragon: klik kanan ikon Laragon > MySQL > *Create database*, atau buka HeidiSQL/phpMyAdmin.
3. **Cek file `.env`** (sudah ada). Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` kalau MySQL-mu memakai kata sandi
   (Laragon bawaan: user `root`, kata sandi kosong).
4. Buka terminal di folder proyek, lalu jalankan:
   ```
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
   ```
5. Buka **http://127.0.0.1:8000**. (Atau, jika Laragon menyalakan virtual host otomatis: `http://vanz-living.test`;
   ubah `APP_URL` di `.env` agar sama.)

> Jangan menjalankan `npm run build` untuk sekadar membuka situs. CSS dan JS sudah jadi di `public/build`.
> `npm install` + `npm run build` hanya perlu jika kamu mengubah file di `resources/css` atau `resources/js`.

## Akun awal (GANTI setelah login pertama)
- Admin: `admin@gmail.com` / `admin123` di `/admin/login`
- Pembeli demo: `user@gmail.com` / `user12345`

## Mengisi toko
- Admin > Produk: ganti 4 produk contoh dengan produkmu (foto, harga, stok).
- Admin > Pengaturan: nama toko, email, WhatsApp. Admin > Kategori: sesuaikan kategori.

## Fitur yang butuh pengaturan di `.env`
- **Midtrans**: isi `MIDTRANS_SERVER_KEY` dan `MIDTRANS_CLIENT_KEY` (mulai dari Sandbox). Notification URL di
  dashboard Midtrans: `https://DOMAIN-KAMU/midtrans/notification`. Kosong = admin konfirmasi pembayaran manual.
- **Model 3D dari foto**: `MODEL3D_SPACE` sudah terisi; isi `HF_TOKEN` (opsional, gratis dari huggingface.co).
  Jalankan `php artisan model3d:check`, lalu di terminal terpisah `php artisan queue:work --timeout=900`.
  Admin > Produk > Edit > Model 3D > *Buat model 3D dari foto* (atau unggah file `.glb`).
- **Chatbot**: isi `CHATBOT_API_KEY`, `CHATBOT_BASE_URL`, `CHATBOT_MODEL` sesuai penyedia AI-mu
  (contoh bawaan: DeepSeek; untuk Ollama lokal: `http://127.0.0.1:11434/v1` dan model `llama3.2:1b`).
  Gunakan kunci API yang BARU; kunci lama yang pernah dibagikan sebaiknya dicabut.

## Catatan
- Foto latar kamar (`public/img/hero-room.jpg`) hanya 736 px; ganti dengan foto >= 1920 px agar tajam.
- Foto lampu contoh berwatermark "PngTree"; ganti dengan versi berlisensi sebelum toko dibuka.
- Nama produk, harga, dan deskripsi contoh hanya placeholder.
- Tes (`php artisan test`) butuh paket dev: jalankan `composer install` dulu.
