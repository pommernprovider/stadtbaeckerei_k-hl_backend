<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalSetting extends Model
{
    protected $guarded = [];

    public static function singleton(): self
    {
        return static::query()->firstOrCreate([], []);
    }
    public static function cached(): self
    {
        return cache()->rememberForever('legal_settings', fn() => static::singleton()->fresh());
    }
}
