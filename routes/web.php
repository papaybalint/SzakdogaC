<?php

use App\Http\Controllers\ProfileController;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {                   //
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'items' => Item::all(),
        'categories' => Category::all(),
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/audiobooks', function () {
    return Inertia::render('Audiobooks');
}); // Teszt  - a 2 Page ful miatt meg nem mukodik -> lsd app.js kikommentel kodreszlet

Route::get('/dvdpage', function () {
    return Inertia::render('DVDPage');
}); // Teszt  - a 2 Page ful miatt meg nem mukodik -> lsd app.js kikommentel kodreszlet



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
