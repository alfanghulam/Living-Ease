<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'stock' => 'integer',
        'discount_price' => 'integer',
        'price' => 'integer',
    ];

        protected static function booted(): void
    {
        $forget = fn () => \Illuminate\Support\Facades\Cache::forget('chatbot.catalog');

        static::saved($forget);
        static::deleted($forget);
    }
    /** Berat & ukuran kemasan sudah lengkap (dibutuhkan untuk menghitung ongkir). */
    public function hasShippingData(): bool
    {
        return $this->weight_grams > 0 && $this->length_cm > 0 && $this->width_cm > 0 && $this->height_cm > 0;
    }

    public function category() { return $this->belongsTo(Category::class); }
    public function brand() { return $this->belongsTo(Brand::class); }
    public function reviews() { return $this->hasMany(Review::class); }

    /** Hitung rating & jumlah ulasan dalam satu query (hindari N+1 di daftar produk). */
    public function scopeWithRating($query)
    {
        return $query->withCount('reviews')->withAvg('reviews', 'rating');
    }

    /** Rata-rata rating 1 desimal, atau null kalau belum ada ulasan. */
    public function getRatingAverageAttribute(): ?float
    {
        $avg = array_key_exists('reviews_avg_rating', $this->attributes)
            ? $this->attributes['reviews_avg_rating']
            : $this->reviews()->avg('rating');

        return $avg === null ? null : round((float) $avg, 1);
    }

    public function getRatingTotalAttribute(): int
    {
        return (int) (array_key_exists('reviews_count', $this->attributes)
            ? $this->attributes['reviews_count']
            : $this->reviews()->count());
    }

    public function hasModel(): bool
    {
        return $this->model_status === 'ready' && filled($this->model_path);
    }

    public function getModelUrlAttribute(): ?string
    {
        return $this->hasModel() ? \App\Support\Media::url($this->model_path) : null;
    }

    public function getImageUrlAttribute()
    {
        if (str_contains($this->image, '/')) {
            return \App\Support\Media::url($this->image);   // sebelumnya: asset('storage/' . $this->image)
        }
        return asset('img/products/' . $this->image);
    }
    /** Harga yang dipakai untuk transaksi (diskon kalau ada). */
    public function getFinalPriceAttribute(): int
    {
        return (int) ($this->discount_price ?: $this->price);
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->discount_price || $this->discount_price >= $this->price) {
            return null;
        }

        return (int) round((1 - ($this->discount_price / $this->price)) * 100);
    }

    public function inStock(): bool
    {
        return $this->stock > 0;
    }
}
