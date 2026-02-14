<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Public Routes
Volt::route('/', 'public.home')->name('home');
Volt::route('/destinations', 'public.destinations.index')->name('destinations.index');
Volt::route('/destinations/{destination:slug}', 'public.destinations.show')->name('destinations.show');
Volt::route('/about', 'public.about')->name('about');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Volt::route('/', 'admin.dashboard')->name('dashboard');
    Volt::route('/destinations', 'admin.destinations.index')->name('destinations.index');
    Volt::route('/destinations/create', 'admin.destinations.form')->name('destinations.create');
    Volt::route('/destinations/{destination}/edit', 'admin.destinations.form')->name('destinations.edit');
    Volt::route('/pages/home', 'admin.pages.home')->name('pages.home');
    Volt::route('/pages/{page:slug}/edit', 'admin.pages.edit')->name('pages.edit');
    Volt::route('/settings', 'admin.settings')->name('settings');
});

require __DIR__.'/settings.php';
