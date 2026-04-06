<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('documents', 'pages/documents')->name('documents');
    Route::view('notifications', 'pages/notifications')->name('notifications');
});

require __DIR__.'/settings.php';
