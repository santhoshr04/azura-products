<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/products');

Route::get('activities', [ActivityController::class, 'index'])->name('activities.index');
Route::resource('products', ProductController::class)->except('show');
Route::resource('categories', CategoryController::class)->except('show');
