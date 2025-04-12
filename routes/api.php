<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\BorrowingMediaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use Database\Seeders\AdminSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('users', UserController::class);
Route::resource('borrowings', BorrowingController::class);
Route::resource('borrowed_media',BorrowingMediaController::class);
Route::resource('items', ItemController::class);
Route::resource('categories', CategoryController::class);
Route::resource('admins', AdminController::class);

Route::post('/borrowings', [BorrowingController::class, 'borrow']);
Route::post('/borrowings/return', [BorrowingController::class, 'returnItem']);

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);

Route::get('/items', [ItemController::class, 'index']);
Route::get('/items/{id}', [ItemController::class, 'show']);
Route::post('/items', [ItemController::class, 'store']);
Route::post('/items', [ItemController::class, 'store'])->name('items.store');
Route::get('/items/{id}', [ItemController::class, 'show'])->name('items.show');

Route::get('/admins', [AdminController::class, 'index']);
Route::get('/admins/{id}', [AdminController::class, 'show']);
Route::post('/admins', [AdminController::class, 'store']);


Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

Route::get('/import', [ImportController::class, 'run']);

Route::get('api/items/{id}', [ItemController::class, 'show']);
Route::get('api/categories/{id}', [CategoryController::class, 'show']);
Route::delete('api/items/{id}', [ItemController::class, 'destroy']);
Route::put('api/items/{id}', [ItemController::class, 'update']);


