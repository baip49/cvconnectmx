<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Portals are handled by Filament Panel Providers
    Route::get('/document/{slug}', [DocumentController::class, 'show'])->name('document.show');
});

require __DIR__.'/settings.php';
