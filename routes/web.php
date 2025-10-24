<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartWebController;
use App\Http\Controllers\CheckoutWebController;
use App\Http\Controllers\PickupController;

Route::get('/', [CatalogController::class, 'home'])->name('home'); // ← NEU: echte Startseite

Route::get('/kategorie/{category:slug}', [CatalogController::class, 'products'])->name('shop.products');
Route::get('/produkte/{product:slug}', [CatalogController::class, 'product'])->name('shop.product');

Route::post('/cart/pickup/windows', [PickupController::class, 'windows'])->name('cart.pickup.windows');
Route::patch('/cart/pickup/select',  [PickupController::class, 'select'])->name('cart.pickup.select');

Route::get('/cart', [CartWebController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartWebController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartWebController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartWebController::class, 'remove'])->name('cart.remove');


Route::post('/cart/pickup/meta', [CartWebController::class, 'pickupMeta'])
    ->name('cart.pickup.meta');


Route::get('/checkout', [CheckoutWebController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutWebController::class, 'store'])->name('checkout.store');

Route::get('/thanks/{order}', [CheckoutWebController::class, 'thanks'])->name('checkout.thanks');
