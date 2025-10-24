<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $guarded = [];

    protected $casts = ['combination' => 'array', 'is_active' => 'bool'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
