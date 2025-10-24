<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOptionValue extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'bool'];

    public function option()
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }
}
