<?php
// app/Models/Order.php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'status'       => OrderStatus::class,
        'pickup_at'    => 'datetime',
        'pickup_end_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'subtotal'     => 'decimal:2',
        'tax_total'    => 'decimal:2',
        'grand_total'  => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Ende des Fensters – jetzt direkter Snapshot aus pickup_end_at */
    public function getPickupEndAttribute(): ?\Carbon\Carbon
    {
        return $this->pickup_end_at; // kompatibler Accessor-Name beibehalten
    }

    /** Menschlich lesbarer Text für das Abholfenster */
    public function getPickupWindowTextAttribute(): string
    {
        if ($this->pickup_window_label) {
            return $this->pickup_window_label;
        }
        $start = $this->pickup_at?->format('H:i');
        $end   = $this->pickup_end_at?->format('H:i');
        return ($start && $end) ? "{$start}–{$end}" : ($start ?? '-');
    }

    public function getTotalGrossAttribute(): ?string
    {
        return $this->grand_total;
    }


    public function formattedTotal(string $attr = 'grand_total'): string
    {
        $value = $this->{$attr};
        $f = $value !== null ? (float) $value : 0.0;
        return number_format($f, 2, ',', '.') . ' €';
    }
}
