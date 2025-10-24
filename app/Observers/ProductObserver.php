<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    public function saved(Product $product): void
    {
        Cache::forget('nav.products.flat');
    }

    public function deleted(Product $product): void
    {
        Cache::forget('nav.products.flat');
    }

    public function restored(Product $product): void
    {
        Cache::forget('nav.products.flat');
    }

    public function forceDeleted(Product $product): void
    {
        Cache::forget('nav.products.flat');
    }
}
