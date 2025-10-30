<?php

namespace App\Models;

use App\Enums\ProductVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $guarded = [];

    protected $casts = [
        'is_published' => 'bool',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'notes_required' => 'bool',

    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function options()
    {
        return $this->hasMany(ProductOption::class);
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function maxOptionLeadDaysHint(): int
    {
        // optionaler, günstiger Hinweis (maximale zusätzliche Tage durch Optionen)
        return (int) DB::table('product_option_values')
            ->join('product_options', 'product_options.id', '=', 'product_option_values.product_option_id')
            ->where('product_options.product_id', $this->id)
            ->selectRaw('MAX(product_option_values.extra_lead_days + (CASE WHEN product_option_values.extra_lead_hours > 0 THEN 1 ELSE 0 END)) as max_extra')
            ->value('max_extra') ?? 0;
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_main')->singleFile()->useDisk('public');
        $this->addMediaCollection('product_gallery')->useDisk('s3');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(400)->height(400)->nonQueued();
        $this->addMediaConversion('web')->width(1200)->height(800)->nonQueued();
    }


    /** Sichtbar im Katalog (zeigt auch “bald verfügbar” an) */
    public function scopePublishedForCatalog($q)
    {
        $q->where('is_published', true)
            ->whereIn('visibility_status', ['active', 'seasonal'])
            // nicht mehr verfügbare ausblenden
            ->where(function ($qq) {
                $qq->whereNull('available_until')
                    ->orWhere('available_until', '>=', now());
            });
    }

    /** Jetzt kaufbar (heute im Fenster) */
    public function scopeAvailableNow($q)
    {
        $q->publishedForCatalog()
            ->where(function ($qq) {
                $qq->where('visibility_status', ['active', 'seasonal']);
            });
    }

    // --- Helpers ---

    public function isAvailableNow(): bool
    {
        if (!$this->is_published || !in_array($this->visibility_status, ['active', 'seasonal'], true)) {
            return false;
        }
        if ($this->available_until && $this->available_until->lt(now())) {
            return false;
        }
        if ($this->available_from && $this->available_from->gt(now())) {
            return false;
        }
        return true;
    }

    /** Darf in den Warenkorb (gleich wie isAvailableNow – separat, falls du später Regeln ergänzt) */
    public function isPurchasable(): bool
    {
        return $this->isAvailableNow();
    }

    public function availabilityBadge(): ?array
    {
        // Rückgabe: ['type'=>'success|warning|danger|info','text'=>'...']
        if ($this->available_until && $this->available_until->lt(now())) {
            return ['type' => 'danger', 'text' => 'Nicht mehr verfügbar'];
        }
        if ($this->available_from && $this->available_from->gt(now())) {
            return ['type' => 'warning', 'text' => 'Ab ' . $this->available_from->translatedFormat('d.m.Y')];
        }
        if ($this->visibility_status === 'seasonal') {
            return ['type' => 'info', 'text' => 'Saisonal'];
        }
        return null;
    }
}
