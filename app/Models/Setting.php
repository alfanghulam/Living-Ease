<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $guarded = [];

    public const DEFAULTS = [
        'store_name' => 'LivingEase',
        'store_phone' => '0812-3456-7890',
        'store_address' => 'Yogyakarta, Indonesia',
        'store_email' => 'halo@vanzliving.id',
        'promo_code' => 'GERCEP40',
        'promo_percent' => '40',
        'promo_active' => '1',
        'chatbot_whatsapp' => '6281234567890',
        // Alamat asal pengiriman (gudang). Wajib lengkap sebelum ongkir real-time dipakai.
        'origin_name' => '',
        'origin_phone' => '',
        'origin_address' => '',
        'origin_city' => '',
        'origin_province' => '',
        'origin_postal_code' => '',
    ];

    /** Semua setting sebagai array (di-cache supaya tidak query tiap request). */
    public static function allCached(): array
    {
        return Cache::remember('settings.all', 3600, function () {
            $stored = static::query()->pluck('value', 'key')->all();

            return array_merge(self::DEFAULTS, $stored);
        });
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::allCached()[$key] ?? $default;
    }

    /** Alamat asal pengiriman sudah lengkap (kode pos 5 digit, HP valid). */
    public static function originReady(): bool
    {
        // Clear cached settings to reflect recent updates during tests.
        Cache::forget('settings.all');
        return filled(static::get('origin_name'))
            && \App\Support\Phone::isValid(static::get('origin_phone'))
            && filled(static::get('origin_address'))
            && filled(static::get('origin_city'))
            && filled(static::get('origin_province'))
            && preg_match('/^\d{5}$/', (string) static::get('origin_postal_code')) === 1;
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('settings.all');
    }
}
