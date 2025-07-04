<?php

use App\Http\Controllers\MagazineUserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('magazine-users', MagazineUserController::class);
});

// Public API endpoint for magazine user submissions
Route::post('/api/magazine-users', [MagazineUserController::class, 'apiStore']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
