<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'stock_applied' => 'boolean',
    ];

    public const STATUS_LABELS = [
        'pending' => 'Menunggu bayar',
        'paid' => 'Dibayar',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    public const STATUS_TONES = [
        'pending' => 'warn',
        'paid' => 'info',
        'shipped' => 'accent',
        'completed' => 'ok',
        'cancelled' => 'danger',
    ];

    public const PAYMENT_LABELS = [
    'bank_transfer' => 'Transfer bank (Virtual Account)',
    'echannel' => 'Mandiri Bill Payment',
    'gopay' => 'GoPay',
    'shopeepay' => 'ShopeePay',
    'qris' => 'QRIS',
    'credit_card' => 'Kartu kredit/debit',
    'cstore' => 'Gerai (Alfamart/Indomaret)',
    ];

    public function channelLabel(): ?string
    {
        if (! $this->payment_channel) {
            return null;
        }

        foreach ((array) config('midtrans.channels', []) as $options) {
            if (isset($options[$this->payment_channel])) {
                $ch = $options[$this->payment_channel];
                return is_array($ch) ? ($ch['label'] ?? null) : $ch;
            }
        }

        return null;
    }

    public function paymentLabel(): ?string
    {
        if ($label = $this->channelLabel()) {
            return $label;
        }

        if (! $this->payment_type) {
            return null;
        }

        return self::PAYMENT_LABELS[$this->payment_type]
            ?? ucwords(str_replace('_', ' ', $this->payment_type));
    }

    /** Transisi status yang diizinkan (state machine sederhana). */
    public const TRANSITIONS = [
        'pending' => ['paid', 'cancelled'],
        'paid' => ['shipped', 'cancelled'],
        'shipped' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst((string) $this->status);
    }

    public function statusTone(): string
    {
        return self::STATUS_TONES[$this->status] ?? 'line';
    }

    public function nextStatuses(): array
    {
        return self::TRANSITIONS[$this->status] ?? [];
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, $this->nextStatuses(), true);
    }

    /**
     * Tandai dibayar dengan aturan yang sama seperti ubah status manual oleh admin:
     * stok dikurangi sekali saja saat pesanan dibayar.
     */
    public function markPaid(): void
    {
        $this->loadMissing('items.product');

        if (! $this->stock_applied) {
            foreach ($this->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->decrement('stock', min($product->stock, $item->qty));
                }
            }
            $this->stock_applied = true;
        }

        $this->status = 'paid';
        $this->paid_at = now();
    }

    public function markCancelled(?string $note = null): void
    {
        $this->loadMissing('items.product');

        if ($this->stock_applied) {
            foreach ($this->items as $item) {
                $item->product?->increment('stock', $item->qty);
            }
            $this->stock_applied = false;
        }

        $this->status = 'cancelled';
        $this->cancelled_at = now();

        if ($note) {
            $this->note = trim(($this->note ? $this->note."\n" : '').'['.now()->format('d/m H:i').'] '.$note);
        }
    }

    public function isAwaitingPayment(): bool
    {
        return $this->status === 'pending';
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $like = '%'.trim($term).'%';

        return $query->where(function (Builder $sub) use ($like) {
            $sub->where('id', 'like', $like)
                ->orWhere('full_name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('tracking_number', 'like', $like);
        });
    }
}
