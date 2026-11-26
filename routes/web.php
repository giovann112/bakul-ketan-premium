<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LocationSwitchController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

// ---------- Publik
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/tentang', [PageController::class, 'about'])->name('about');
Route::post('/lokasi/pilih', [LocationSwitchController::class, 'switch'])->name('location.switch');

Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/tambah/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/keranjang/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/keranjang/{key}', [CartController::class, 'remove'])->name('cart.remove');

Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/sukses/{code}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('/lacak', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/lacak', [TrackingController::class, 'search'])->middleware('throttle:10,1')->name('tracking.search');

// ---------- Admin
Route::get('/admin/login', [Admin\AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [Admin\AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
Route::post('/admin/logout', [Admin\AuthController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', Admin\ProductController::class)->except('show');
    Route::resource('categories', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('toppings', Admin\ToppingController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('/stocks', [Admin\StockController::class, 'index'])->name('stocks.index');
    Route::put('/stocks/location/{location}', [Admin\StockController::class, 'updateLocation'])->name('stocks.location');
    Route::put('/stocks/today/{location}', [Admin\StockController::class, 'updateToday'])->name('stocks.today');
    Route::post('/stocks/reset/{location}', [Admin\StockController::class, 'resetToday'])->name('stocks.reset');

    Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
});
