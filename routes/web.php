<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

use App\Http\Controllers\DocumentController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Portals are handled by Filament Panel Providers
    Route::get('/document/{slug}', [DocumentController::class, 'show'])->name('document.show');
});

require __DIR__.'/settings.php';
