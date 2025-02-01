<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\BorrowingMediaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//Route::get('/user', function (Request $request) {
    //return $request->user();
//})->middleware('auth:sanctum');

Route::resource('users', UsersController::class);
Route::resource('borrowings', BorrowingController::class);
Route::resource('borrowed_media',BorrowingMediaController::class);
Route::resource('items', ItemController::class);
Route::resource('categories', CategoryController::class);
Route::resource('admins', AdminController::class);
