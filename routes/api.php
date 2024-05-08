<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('', [CategoryController::class, 'index'])->name('category.list');

Route::prefix('category')->controller(CategoryController::class)->name('category.')->group(function () {
    Route::get('{category}', 'show')->name('show');
});

Route::prefix('product')->controller(ProductController::class)->name('product.')->group(function () {
    Route::get('{product}', 'show')->name('show');
});

Route::prefix('user')->controller(UserController::class)->name('user.')->group(function () {
    Route::get('{user}/orders', 'orders')->name('orders');
});

Route::prefix('order')
    ->controller(OrderController::class)
    ->name('order.')
    ->group(function () {
        Route::put('/', 'store')
            ->name('store');

        Route::get('{order}', 'show')
            ->name('show');

        Route::patch('{order}', 'update')
            ->name('update');
    });