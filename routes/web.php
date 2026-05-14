<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CauldronController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Гостинецъ
|--------------------------------------------------------------------------
*/

/* ============== ПУБЛИЧНЫЕ ============== */

Route::get('/', fn() => view('home'))->name('home');

// Каталог + страница товара + поиск
Route::get('/catalog',        [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/{slug}', [CatalogController::class, 'show'])->name('product');


/* ============== КОРЗИНА ============== */
Route::get('/cart',           [CartController::class, 'index'])->name('cart');
Route::post('/cart/add',       [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');


/* ============== ОФОРМЛЕНИЕ ЗАКАЗА ============== */
Route::get('/checkout',                 [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout',                 [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/{order}/return',  [CheckoutController::class, 'return'])->name('checkout.return');
Route::get('/checkout/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/{order}/fail',    [CheckoutController::class, 'fail'])->name('checkout.fail');


/* ============== WEBHOOK ЮКАССЫ ============== */
// Этот URL надо прописать в личном кабинете ЮКассы.
// CSRF отключён в VerifyCsrfToken::$except.
Route::post('/yookassa/webhook', [PaymentController::class, 'webhook'])->name('yookassa.webhook');


/* ============== КОТЕЛОК (кастомное варенье) ============== */
Route::get('/cauldron',         [CauldronController::class, 'index'])->name('cauldron');
Route::post('/cauldron/preview', [CauldronController::class, 'preview'])->name('cauldron.preview');

Route::middleware('auth')->group(function () {
    Route::post('/cauldron',         [CauldronController::class, 'store'])->name('cauldron.store');
    Route::get('/cauldron/grimoire', [CauldronController::class, 'grimoire'])->name('cauldron.grimoire');
});


/* ============== АУТЕНТИФИКАЦИЯ ============== */
Route::middleware('guest')->group(function () {
    Route::post('/login',    [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')->name('logout');


/* ============== КАБИНЕТ ПОЛЬЗОВАТЕЛЯ ============== */
Route::middleware('auth')->group(function () {
    Route::view('/account', 'account.index')->name('account');
    Route::view('/orders',  'account.orders')->name('orders');
});


/* ============== АДМИНКА ============== */
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::view('/', 'admin.dashboard')->name('dashboard');
    });
