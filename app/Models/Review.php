<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Review extends Model
{
    protected $guarded = [];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function order() { return $this->belongsTo(Order::class); }

    /** "Rizky Aditya" -> "Rizky A." (nama lengkap pembeli tidak ditampilkan publik). */
    public function displayName(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->user?->name)) ?: [];
        $first = $parts[0] ?? '';

        if ($first === '') {
            return 'Pembeli';
        }

        return count($parts) > 1
            ? $first.' '.Str::upper(Str::substr(end($parts), 0, 1)).'.'
            : $first;
    }
}
