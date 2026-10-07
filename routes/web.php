<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::livewire('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('/customers', 'customers')->name('customers');

    Route::livewire('/templates', 'templates')->name('message-templates');

    Route::livewire('/music', 'music')->name('music');

    Route::livewire('/about', 'about')->name('about');

    Route::livewire('/updates', 'updates')->name('updates');

    Route::middleware('admin')->group(function () {
        Route::livewire('admin/overview', 'admin.overview')
            ->name('admin.overview');

        Route::livewire('admin/members', 'admin.members')
            ->name('admin.members');

        Route::livewire('admin/music', 'admin.music')
            ->name('admin.music');
        Route::livewire('admin/request-music', 'admin.request-music')
            ->name('admin.request-music');
    });
});

require __DIR__.'/settings.php';
