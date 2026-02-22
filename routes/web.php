<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Public Routes
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'id', 'es'])) {
        session()->put('locale', $locale);
    }

    return redirect()->back();
})->name('lang.switch');

Volt::route('/', 'public.home')->name('home');
Volt::route('/destinations', 'public.destinations.index')->name('destinations.index');
Volt::route('/destinations/{destination:slug}', 'public.destinations.show')->name('destinations.show');
Volt::route('/about', 'public.about')->name('about');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Volt::route('/', 'admin.dashboard')->name('dashboard');
    Volt::route('/bookings', 'admin.bookings.index')->name('bookings');
    Volt::route('/destinations', 'admin.destinations.index')->name('destinations.index');
    Volt::route('/destinations/create', 'admin.destinations.form')->name('destinations.create');
    Volt::route('/destinations/{destination}/edit', 'admin.destinations.form')->name('destinations.edit');
    Volt::route('/pages/home', 'admin.pages.home')->name('pages.home');
    Volt::route('/pages/{page:slug}/edit', 'admin.pages.edit')->name('pages.edit');

    Volt::route('/settings', 'admin.settings')->name('settings');

    // Admin Only
    Route::middleware(['admin'])->group(function () {
        Volt::route('/users', 'admin.users.index')->name('users.index');
        Volt::route('/users/create', 'admin.users.form')->name('users.create');
        Volt::route('/users/{user}/edit', 'admin.users.form')->name('users.edit');
    });
});

require __DIR__.'/settings.php';
