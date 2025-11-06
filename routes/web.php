<?php

use App\Http\Controllers\ShopifyController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::prefix('auth')->group(function () {
    // Route::get('/install', [AuthController::class, 'install'])->name('shopify.install');
    // Route::get('/callback', [AuthController::class, 'callback'])->name('shopify.callback');
    Route::get('/redirect', [ShopifyController::class, 'redirect'])->name('shopify.redirect');
Route::get('/callback', [ShopifyController::class, 'callback'])->name('shopify.callback');
});
Route::view('/{any}', 'react')->where('any', '.*');
