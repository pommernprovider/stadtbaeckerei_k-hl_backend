<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class BrandSetting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

    protected $casts = ['social_links' => 'array'];

    public static function singleton(): self
    {
        return static::query()->firstOrCreate([], []);
    }
    public static function cached(): self
    {
        return cache()->rememberForever('branding_settings', fn() => static::singleton()->fresh());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->useDisk('public')->singleFile();
        $this->addMediaCollection('favicon')->useDisk('public')->singleFile();
    }
    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('web')->performOnCollections('logo')->format('webp')->nonQueued();
        $this->addMediaConversion('32')->performOnCollections('favicon')->width(32)->height(32)->format('png')->nonQueued();
        $this->addMediaConversion('180')->performOnCollections('favicon')->width(180)->height(180)->format('png')->nonQueued();
    }
}
