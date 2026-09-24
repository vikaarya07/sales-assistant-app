<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::livewire('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('/customers', 'customers')->name('customers');

    Route::livewire('/templates', 'templates')->name('message-templates');

    Route::livewire('/about', 'about')->name('about');

    Route::livewire('/updates', 'updates')->name('updates');
});

require __DIR__.'/settings.php';
