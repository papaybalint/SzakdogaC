<?php

use App\Http\Controllers\ProfileController;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/items/create', function () {
    return Inertia::render('AddItem');
})->name('items.create');

Route::get('/borrowed_media', function () {
    return Inertia::render('Borrowed_media');
})->middleware(['auth', 'verified'])->name('borrowed_media');

Route::get('/item_details', function (Request $request) {
    $itemid = $request->query('object');
    $item = Item::find($itemid);

    return Inertia::render('ItemDetails', [
        'item' => $item,
    ]
);
})->middleware(['auth', 'verified'])->name('item_details');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
