<?php

use App\Http\Controllers\CrateController;
use App\Http\Controllers\DiscogsServiceController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('oauth/discogs/connect', [DiscogsServiceController::class, 'create'])
    ->name('discogs.create')
    ->middleware('auth');;
Route::post('oauth/discogs/callback', [DiscogsServiceController::class, 'store'])
    ->name('discogs.store.post')
    ->middleware('auth');
Route::get('oauth/discogs/callback', [DiscogsServiceController::class, 'store'])
    ->name('discogs.store.get')
    ->middleware('auth');;

Route::delete('discogs/delete', [DiscogsServiceController::class, 'destroy'])
    ->name('discogs.destroy')
    ->middleware('auth');;

Route::resource('inventories', InventoryController::class);

Route::resource('crates', CrateController::class)
    ->only(['index','store', 'update', 'destroy']);

require __DIR__.'/auth.php';
