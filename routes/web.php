<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

// no_store: the page changes after login, and the browser's back button must not show the guest version.
Route::get('/', WelcomeController::class)->middleware('cache.headers:no_store;private')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Portals are handled by Filament Panel Providers
    Route::get('/document/{slug}', [DocumentController::class, 'show'])->name('document.show');
});

require __DIR__.'/settings.php';
