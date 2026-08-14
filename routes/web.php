<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('products', Admin\ProductController::class);
        Route::resource('categories', Admin\CategoryController::class);
    });
