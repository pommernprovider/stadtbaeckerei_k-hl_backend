<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Warenkorb Anzeige Mini
        View::composer('partials.header', function ($view) {
            $cart = session('cart_web', ['items' => []]);

            $ids = collect($cart['items'])->pluck('product_id')->unique()->filter();
            $products = Product::query()->whereIn('id', $ids)->get()->keyBy('id');

            $items = [];
            $count = 0;
            $total = 0.0;

            foreach ($cart['items'] as $row) {
                $p    = $products[$row['product_id']] ?? null;
                $qty  = (int)($row['qty'] ?? 1);
                $unit = (float)($row['unit_price'] ?? ($p?->base_price ?? 0));
                $sum  = round($unit * $qty, 2);

                $items[] = [
                    'name' => $p?->name ?? 'Artikel',
                    'url'  => $p ? route('shop.product', $p) : '#',
                    'img'  => $p?->getFirstMediaUrl('product_main', 'thumb') ?: $p?->getFirstMediaUrl('product_main'),
                    'qty'  => $qty,
                    'sum'  => $sum,
                ];

                $count += $qty;
                $total += $sum;
            }

            $view->with('miniCart', [
                'count' => $count,
                'total' => round($total, 2),
                'items' => $items,
            ]);
        });

        // Navigation Produkte Auflisten
        View::composer('*', function ($view) {
            $navProducts = Cache::remember('nav.products.flat', now()->addMinutes(10), function () {
                return Product::query()
                    ->where('is_published', true)
                    ->where('visibility_status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('available_from')->orWhere('available_from', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('available_until')->orWhere('available_until', '>=', now());
                    })
                    ->orderBy('position')
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug']);
            });

            $view->with('navProducts', $navProducts);
        });

        Product::observe(ProductObserver::class);
    }
}
