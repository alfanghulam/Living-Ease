<?php

/*
|--------------------------------------------------------------------------
| Model 3D otomatis (Hugging Face Spaces / Gradio)
|--------------------------------------------------------------------------
| Admin cukup mengunggah satu foto produk. Foto dikirim ke sebuah Space
| Hugging Face yang mengubahnya jadi model 3D (.glb) yang bisa diputar 360°
| di halaman produk.
|
| Space publik gratis, tapi punya antrean dan kuota harian (ZeroGPU). Token
| Hugging Face (gratis) menaikkan kuota. Jalankan `php artisan model3d:check`
| untuk memastikan Space & endpoint di bawah masih sesuai.
*/

return [
    'enabled' => env('MODEL3D_ENABLED', true),

    // "pemilik/nama-space" atau URL penuh, mis. https://pemilik-nama-space.hf.space
    'space' => env('MODEL3D_SPACE', 'stabilityai/TripoSR'),

    // Opsional tapi disarankan: https://huggingface.co/settings/tokens (token "read")
    'token' => env('HF_TOKEN'),

    // Kosongkan agar terdeteksi otomatis ("/gradio_api" untuk Gradio 5).
    'api_prefix' => env('MODEL3D_API_PREFIX'),

    // Detik. Antrean ZeroGPU bisa lama.
    'timeout' => (int) env('MODEL3D_TIMEOUT', 600),

    /*
    | Langkah pemanggilan API Space, berurutan.
    |   '@image'    = foto produk yang sudah diunggah ke Space
    |   '@previous' = file keluaran langkah sebelumnya
    | Default ini untuk stabilityai/TripoSR: /preprocess (hapus latar) lalu /generate.
    | Ganti kalau kamu memakai Space lain.
    */
    'steps' => [
        ['api' => 'preprocess', 'data' => ['@image', true, 0.85]],
        ['api' => 'generate', 'data' => ['@previous', 256]],
    ],
];
