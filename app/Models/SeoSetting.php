<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SeoSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    protected $casts = ['meta_tags' => 'array'];

    public static function singleton(): self
    {
        return static::query()->firstOrCreate([], []);
    }
    public static function cached(): self
    {
        return cache()->rememberForever('seo_settings', fn() => static::singleton()->fresh());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('og_image')->useDisk('public')->singleFile();
    }
}
