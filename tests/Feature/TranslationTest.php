<?php

use App\Models\Destination;
use App\Models\Page;
use Illuminate\Support\Facades\App;

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('can create a destination with translation arrays', function () {
    $destination = Destination::create([
        'title' => [
            'en' => 'Test Destination EN',
            'id' => 'Destinasi Tes ID',
            'es' => 'Destino de Prueba ES',
        ],
        'slug' => 'test-destination-translation',
        'location' => ['en' => 'Bali'],
        'description' => ['en' => 'Description'],
        'price' => 1000,
    ]);

    expect($destination->id)->toBeGreaterThan(0);
});

test('returns correct translation based on locale', function () {
    $destination = Destination::create([
        'title' => [
            'en' => 'English Title',
            'id' => 'Judul Indonesia',
            'es' => 'Título en Español',
        ],
        'slug' => 'test-locale-switch',
        'location' => ['en' => 'Bali'],
        'description' => ['en' => 'Description'],
        'price' => 1000,
    ]);

    App::setLocale('en');
    expect($destination->title)->toBe('English Title');

    App::setLocale('id');
    expect($destination->title)->toBe('Judul Indonesia');

    App::setLocale('es');
    expect($destination->title)->toBe('Título en Español');
});

test('page model also handles translations correctly', function () {
    $page = Page::create([
        'title' => [
            'en' => 'Page Title',
            'id' => 'Judul Halaman',
        ],
        'slug' => 'test-page',
        'content' => [
            'en' => 'Content EN',
            'id' => 'Konten ID',
        ],
    ]);

    App::setLocale('en');
    expect($page->title)->toBe('Page Title');
    expect($page->content)->toBe('Content EN');

    App::setLocale('id');
    expect($page->title)->toBe('Judul Halaman');
    expect($page->content)->toBe('Konten ID');
});

test('translations fall back correctly', function () {
    $destination = Destination::create([
        'title' => [
            'en' => 'Only EN Title',
        ],
        'slug' => 'test-fallback',
        'location' => ['en' => 'Bali'],
        'description' => ['en' => 'Description'],
        'price' => 1000,
    ]);

    // Set locale to Indonesian, should fall back to English
    App::setLocale('id');
    expect($destination->title)->toBe('Only EN Title');

    // Set locale to something else, should still fall back to English or first
    App::setLocale('fr');
    expect($destination->title)->toBe('Only EN Title');
});
