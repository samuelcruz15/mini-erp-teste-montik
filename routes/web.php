<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CuponController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProfileController;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Main route - list products (public)
Route::get('/', [ProductController::class, 'index'])->name('home');

// Public product routes
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{product}/variation-price', [ProductController::class, 'getVariationPrice'])->name('products.variation-price');

// Cart routes (public - anyone can add to cart and view cart)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::get('/count', [CartController::class, 'getCartCount'])->name('count');
    Route::get('/', [CartController::class, 'index'])->name('index');
});

// Cart routes (authenticated users only)
Route::middleware(['auth', 'user'])->prefix('cart')->name('cart.')->group(function () {
    Route::patch('/update', [CartController::class, 'update'])->name('update');
    Route::delete('/remove', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::post('/apply-coupon', [CartController::class, 'applyCupon'])->name('apply-coupon');
    Route::delete('/remove-coupon', [CartController::class, 'removeCupon'])->name('remove-coupon');
    Route::post('/check-zipcode', [CartController::class, 'checkCep'])->name('check-zipcode');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
});

// Order completion routes (users only)
Route::middleware(['auth', 'user'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('my-orders');
    Route::get('/my-orders/{orderId}', [OrderController::class, 'show'])->name('my-orders.show');
});

// User profile routes (users only)
Route::middleware(['auth', 'user'])->prefix('my-account')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::get('/info', [ProfileController::class, 'show'])->name('show');
});

// Address routes (users only)
Route::middleware(['auth', 'user'])->prefix('addresses')->name('addresses.')->group(function () {
    Route::get('/', [AddressController::class, 'index'])->name('index');
    Route::get('/create', [AddressController::class, 'create'])->name('create');
    Route::post('/', [AddressController::class, 'store'])->name('store');
    Route::get('/{address}', [AddressController::class, 'show'])->name('show');
    Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
    Route::put('/{address}', [AddressController::class, 'update'])->name('update');
    Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
    Route::patch('/{address}/default', [AddressController::class, 'setDefault'])->name('set-default');
});

// Admin routes (requires admin)
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin dashboard
    Route::get('/admin', function () {
        return redirect()->route('admin.products.index');
    })->name('admin.dashboard');
    
    // Product management
    Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/admin/products/{product}/variations', [ProductController::class, 'addVariation'])->name('products.add-variation');
    Route::post('/admin/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::patch('/admin/stocks/{stock}/update', [ProductController::class, 'updateStock'])->name('admin.stocks.update');
    Route::delete('/admin/stocks/{stock}/remove', [ProductController::class, 'removeStock'])->name('admin.stocks.remove');
    
    // Order management
    Route::get('/admin/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/admin/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::delete('/admin/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    
    // Coupon management
    Route::prefix('admin')->name('coupons.')->group(function () {
        Route::get('/coupons', [CuponController::class, 'index'])->name('index');
        Route::get('/coupons/create', [CuponController::class, 'create'])->name('create');
        Route::post('/coupons', [CuponController::class, 'store'])->name('store');
        Route::get('/coupons/{cupon}/edit', [CuponController::class, 'edit'])->name('edit');
        Route::put('/coupons/{cupon}', [CuponController::class, 'update'])->name('update');
        Route::delete('/coupons/{cupon}', [CuponController::class, 'destroy'])->name('destroy');
    });
});


