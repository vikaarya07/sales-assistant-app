<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::view('/about', 'pages/about')->name('about');

Route::view('/updates', 'pages/updates')->name('updates');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::livewire('user/dashboard', 'dashboard')->name('user.dashboard');

    Route::livewire('user/customers', 'customers')->name('user.customers');

    Route::livewire('user/templates', 'templates')->name('user.message-templates');

    Route::livewire('user/interest-calculator', 'interest-calculator')->name('user.interest-calculator');

    Route::livewire('user/music', 'music')->name('user.music');

    Route::livewire('user/about', 'about')->name('user.about');

    Route::livewire('user/updates', 'updates')->name('user.updates');

    Route::middleware('admin')->group(function () {
        Route::livewire('admin/overview', 'admin.overview')
            ->name('admin.overview');

        Route::livewire('admin/members', 'admin.members')
            ->name('admin.members');

        Route::livewire('admin/music', 'admin.music')
            ->name('admin.music');
        Route::livewire('admin/request-music', 'admin.request-music')
            ->name('admin.request-music');

        Route::livewire('admin/updates', 'admin.updates')->name('admin.updates');
        Route::livewire('admin/planned-updates', 'admin.planned-updates')->name('admin.planned-updates');
    });
});

require __DIR__.'/settings.php';
