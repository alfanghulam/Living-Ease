<?php

namespace App\Support;

/** Nomor HP Indonesia: simpan dalam bentuk 08xxxxxxxxxx (format yang diminta kurir). */
class Phone
{
    public static function normalize(?string $value): string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        if (str_starts_with($digits, '62')) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }

    public static function isValid(?string $normalized): bool
    {
        return (bool) preg_match('/^08\d{8,11}$/', (string) $normalized);
    }
}