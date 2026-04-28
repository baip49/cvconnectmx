<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Portals are handled by Filament Panel Providers
});

require __DIR__.'/settings.php';
