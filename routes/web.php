<?php

use App\Http\Controllers\MagazineUserController;
use Illuminate\Http\Request;
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
Route::options('/api/magazine-users', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Accept');
});

// Dynamic JavaScript file with env variables
Route::get('/js/magazine-embed.js', function (Request $request) {
    $apiUrl = $request->getSchemeAndHttpHost() . '/api/magazine-users';
    
    $js = view('embed.magazine-embed', compact('apiUrl'))->render();
    
    return response($js)
        ->header('Content-Type', 'application/javascript')
        ->header('Cache-Control', 'public, max-age=3600')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET')
        ->header('Access-Control-Allow-Headers', 'Content-Type');
});

// Iframe embed route
Route::get('/embed/magazine-subscription', [MagazineUserController::class, 'embedIframe'])->name('embed.iframe');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
