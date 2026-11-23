<?php

namespace App\Support;

class Media
{
    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $path = ltrim($path, '/');

        // Tautan public/storage berfungsi -> pakai; kalau tidak (hilang/rusak/folder lain) -> rute /media
        return is_file(public_path('storage/'.$path))
            ? asset('storage/'.$path)
            : route('media', ['path' => $path]);
    }
}