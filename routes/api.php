<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopifyController;
use App\Http\Controllers\ShopifyWebhookController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\VerifyShopifyAuth;

Route::middleware([VerifyShopifyAuth::class])->group(function () {
    Route::get('/dashboard-summary', [ProductController::class, 'summary']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/sync-logs', [ProductController::class, 'syncLogs']);
    Route::post('/products/sync', [ProductController::class, 'sync']);
    Route::post('/products/sync', [ProductController::class, 'sync']);
    Route::post('/collections/sync', [ProductController::class, 'syncCollections']);
});

Route::post('/shopify/webhook', [ShopifyWebhookController::class, 'handle']);
