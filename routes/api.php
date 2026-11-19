<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Customer\RegisterController;
use App\Http\Controllers\Api\Customer\LoginController;
use App\Http\Controllers\Api\Customer\LogoutController;
use App\Http\Controllers\Api\Customer\MyOrderController;
use App\Http\Controllers\Api\Customer\MyProfileController;
use App\Http\Controllers\Api\Customer\RatingController;
use App\Http\Controllers\Api\Customer\CartController;
use App\Http\Controllers\Api\Public\SliderController;
use App\Http\Controllers\Api\Public\CategoryController;
use App\Http\Controllers\Api\Public\ProductController;
use App\Http\Controllers\Api\Public\RajaOngkirController;
use App\Http\Controllers\Api\Public\CheckoutController;
use App\Http\Controllers\Api\Public\CallbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Route API Callback Midtrans
Route::post('/callback', [CallbackController::class, 'index'])->name('public.callback');


// Route group khusus Customer (Akses URL: /api/customer/...)
Route::group(['prefix' => 'customer'], function () {

    Route::post('/register', RegisterController::class)->name('customer.register');
    Route::post('/login', LoginController::class)->name('customer.login');

    // Route wajib Authenticated (Bearer Token)
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/logout', LogoutController::class)->name('customer.logout');

        // Orders
        Route::get('/my-orders', [MyOrderController::class, 'index'])->name('customer.my-orders');
        Route::get('/my-orders/{snap_token}', [MyOrderController::class, 'show'])->name('customer.my-orders.show');

        // Profile
        Route::get('/profile', [MyProfileController::class, 'index'])->name('customer.profile');
        Route::post('/profile', [MyProfileController::class, 'update'])->name('customer.profile.update');

        // Ratings
        Route::post('/ratings', RatingController::class)->name('customer.ratings');

        // Carts
        Route::get('/carts', [CartController::class, 'index'])->name('customer.carts.index');
        Route::post('/carts', [CartController::class, 'store'])->name('customer.carts.store');
        Route::post('/carts/increment', [CartController::class, 'incrementCart'])->name('customer.carts.increment');
        Route::post('/carts/decrement', [CartController::class, 'decrementCart'])->name('customer.carts.decrement');
        Route::delete('/carts/{id}', [CartController::class, 'removeCart'])->name('customer.carts.destroy');
    });

});


// Route group khusus API Public (Akses URL: /api/public/...)
Route::group(['prefix' => 'public'], function () {

    // Sliders Banner
    Route::get('/sliders', SliderController::class)->name('public.sliders');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('public.categories.index');
    Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('public.categories.show');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('public.products.index');
    Route::get('/products-popular', [ProductController::class, 'productPopular'])->name('public.products.popular');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('public.products.show');

    // Raja Ongkir & Search Destination
    Route::get('/search-destination', [RajaOngkirController::class, 'searchDestination'])->name('public.search-destination');
    Route::get('/provinces', [RajaOngkirController::class, 'getProvinces'])->name('public.provinces');
    Route::get('/cities', [RajaOngkirController::class, 'getCities'])->name('public.cities');
    Route::post('/check-ongkir', [RajaOngkirController::class, 'checkOngkir'])->name('public.check-ongkir');

    // RESTful API Checkout
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('public.checkout');

});