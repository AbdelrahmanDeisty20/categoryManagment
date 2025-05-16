<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/categories', [CategoryController::class, 'index'])->name('index');

Route::get('/categories/paginate', [CategoryController::class, 'ajaxPaginate'])->name('categories.paginate');

Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store');

Route::post('/categories/update', [CategoryController::class, 'update'])->name('categories.update');

Route::delete('/categories/delete', [CategoryController::class, 'destroy'])->name('categories.destroy');

Route::get('/categories/search', [CategoryController::class, 'search'])->name('categories.search');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
