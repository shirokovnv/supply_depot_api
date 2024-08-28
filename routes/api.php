<?php

use App\Http\Controllers\Api\v1\ProductController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function() {
    Route::group(['prefix' => 'products'], function() {
        Route::post('/documents', [ProductController::class, 'storeDocument'])
            ->name('api.v1.products.documents.store');

        Route::get('/history', [ProductController::class, 'listHistory'])
            ->name('api.v1.products.history');

        Route::get('/{id}/history', [ProductController::class, 'itemHistory'])
            ->name('api.v1.products.item.history');

        Route::get('/inventory', [ProductController::class, 'showInventory'])
            ->name('api.v1.products.inventory');
    });
});
