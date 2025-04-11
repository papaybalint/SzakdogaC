<?php

use App\Http\Controllers\ProfileController;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ItemController;

Route::get('/', function () {
    return Inertia::render('Welcome');
});


Route::post('/items', [ItemController::class, 'store'])->middleware(['auth'])->name('items.store');


Route::get('/items/create', function () {
    return Inertia::render('AddItem');
})->name('items.create');

Route::get('/borrowed_media', function () {
    return Inertia::render('Borrowed_media');
})->middleware(['auth', 'verified'])->name('borrowed_media');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
