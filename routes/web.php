<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductModelController as AdminProductModelController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MidtransNotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WelcomeController;
use App\Models\Product;



// ================= HALAMAN WELCOME & SHOP =================
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::get('/shop', [ProductController::class, 'index'])->name('home');

Route::get('/produk/{product}', [ProductController::class, 'show'])->name('products.show');

// ================= CHATBOT AI =================
Route::post('/chat', [ChatController::class, 'send'])->middleware('throttle:20,1')->name('chat.send');
Route::get('/chat/history', [ChatController::class, 'history'])->middleware('throttle:60,1')->name('chat.history');

// ================= AUTH CUSTOMER =================
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================= HALAMAN USER (butuh login) =================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/profile/photo', [UserController::class, 'updatePhoto'])->name('user.photo');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/beli-sekarang/{product}', [CheckoutController::class, 'buyNow'])->name('checkout.buyNow');

    // Pembayaran Midtrans & ulasan produk
    Route::get('/pembayaran/{order}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/produk/{product}/ulasan', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/ulasan/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

Route::get('/media/{path}', [MediaController::class, 'show'])->where('path', '.*')->name('media');
// Webhook Midtrans (tanpa login & tanpa CSRF; keamanan lewat signature_key)
Route::post('/midtrans/notification', [MidtransNotificationController::class, 'handle'])
    ->middleware('throttle:120,1')
    ->name('midtrans.notification');

// ================= ADMIN =================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Pesanan
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/export', [AdminOrderController::class, 'exportCsv'])->name('orders.export');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

        // Kategori & brand
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/brands', [AdminBrandController::class, 'index'])->name('brands.index');
        Route::post('/brands', [AdminBrandController::class, 'store'])->name('brands.store');
        Route::put('/brands/{brand}', [AdminBrandController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{brand}', [AdminBrandController::class, 'destroy'])->name('brands.destroy');

        // Pelanggan
        Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{user}', [AdminCustomerController::class, 'show'])->name('customers.show');

        // Inbox chat
        Route::get('/chat', [AdminChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/{session}', [AdminChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{session}/reply', [AdminChatController::class, 'reply'])->name('chat.reply');

        // Pengaturan
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{product}/model', [AdminProductModelController::class, 'generate'])->name('products.model.generate');
        Route::get('/products/{product}/model/status', [AdminProductModelController::class, 'status'])->name('products.model.status');
        Route::post('/products/{product}/model/upload', [AdminProductModelController::class, 'upload'])->name('products.model.upload');
        Route::delete('/products/{product}/model', [AdminProductModelController::class, 'destroy'])->name('products.model.destroy');
        Route::patch('/products/{product}/toggle', [AdminProductController::class, 'toggleActive'])->name('products.toggle');
    });
});