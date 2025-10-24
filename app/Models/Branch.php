<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Branch extends Model implements HasMedia
{

    use InteractsWithMedia;
    protected $guarded = [];

    public function openingHours(): HasMany
    {
        return $this->hasMany(BranchOpeningHour::class);
    }

    public function closures(): HasMany
    {
        return $this->hasMany(BranchClosure::class);
    }

    public function pickupWindows(): HasMany
    {
        return $this->hasMany(BranchPickupWindow::class);
    }

    public function pickupWindowOverrides(): HasMany
    {
        return $this->hasMany(BranchPickupWindowOverride::class);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(400)->height(400)->nonQueued();
        $this->addMediaConversion('web')->width(1200)->height(800)->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('branch_images')
            ->useDisk('public');
    }
}
