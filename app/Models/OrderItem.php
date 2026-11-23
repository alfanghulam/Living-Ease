<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function title(): string
    {
        return $this->product_title ?: ($this->product?->title ?? 'Produk dihapus');
    }

    public function imageUrl(): string
    {
        if ($this->product_image) {
            return str_contains($this->product_image, '/')
                ? \App\Support\Media::url($this->product_image)
                : asset('img/products/'.$this->product_image);
        }

        return $this->product?->image_url ?? asset('img/no-image.png');
    }

    public function subtotal(): int
    {
        return $this->qty * $this->price;
    }
}
