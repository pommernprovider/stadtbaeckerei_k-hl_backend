<?php
// app/Http/Controllers/CatalogController.php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class CatalogController extends Controller
{
    public function home()
    {
        $cats = Category::query()
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('name')
            ->take(8)
            ->get();

        // Startseite: nur JETZT verfügbare Highlights
        $featured = Product::query()
            ->availableNow()
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('shop.index', compact('cats', 'featured'));
    }

    public function categories()
    {
        $cats = Category::query()
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        return view('shop.categories', compact('cats'));
    }

    public function products(Category $category)
    {
        // Kategorieseite: sowohl jetzt verfügbare als auch “bald verfügbar”
        $products = Product::query()
            ->where('category_id', $category->id)
            ->publishedForCatalog()
            ->orderByRaw("CASE WHEN available_from IS NOT NULL AND available_from > NOW() THEN 1 ELSE 0 END ASC")
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        return view('shop.products', compact('category', 'products'));
    }

    public function product(Product $product)
    {
        abort_unless(
            $product->is_published && in_array($product->visibility_status, ['active', 'seasonal'], true)
                && (!$product->available_until || $product->available_until->gte(now())),
            404
        );

        $product->load([
            'options' => fn($q) => $q->orderBy('position'),
            'options.activeValues',
            'category',
            'tags',
        ]);

        $related = Product::query()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->publishedForCatalog()
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('shop.product', compact('product', 'related'));
    }
}
