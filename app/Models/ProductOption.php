<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    protected $guarded = [];

    protected $casts = ['is_required' => 'bool'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function values()
    {
        return $this->hasMany(\App\Models\ProductOptionValue::class)->orderBy('position');
    }
    public function activeValues()
    {
        return $this->values()->where('is_active', true);
    }
}
