<?php

use App\Models\Destination;
use App\Models\Language;
use App\Models\Page;
use Illuminate\Support\Facades\App;

beforeEach(function () {
    Language::create(['code' => 'en', 'name' => 'English', 'is_active' => true]);
    Language::create(['code' => 'id', 'name' => 'Indonesian', 'is_active' => true]);
    Language::create(['code' => 'es', 'name' => 'Spanish', 'is_active' => true]);
});

test('can create a destination with translation arrays', function () {
    // Debug: ensure languages are present
    if (Language::count() === 0) {
        Language::create(['code' => 'en', 'name' => 'English', 'is_active' => true]);
        Language::create(['code' => 'id', 'name' => 'Indonesian', 'is_active' => true]);
        Language::create(['code' => 'es', 'name' => 'Spanish', 'is_active' => true]);
    }

    $destination = Destination::create([
        'title' => [
            'en' => 'Test Destination EN',
            'id' => 'Destinasi Tes ID',
            'es' => 'Destino de Prueba ES',
        ],
        'slug' => 'test-destination-translation-'.uniqid(),
        'location' => ['en' => 'Bali'],
        'description' => ['en' => 'Description'],
        'price' => 1000,
    ]);

    expect($destination->id)->toBeGreaterThan(0);
    expect($destination->translations()->count())->toBe(3);
    expect($destination->title)->toBe('Test Destination EN');
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

test('destination sub-models handle translations correctly', function () {
    $destination = Destination::create([
        'title' => ['en' => 'Main title'],
        'slug' => 'test-sub-models',
        'location' => ['en' => 'Bali'],
        'description' => ['en' => 'Description'],
        'price' => 1000,
    ]);

    // Test Itinerary
    $itinerary = $destination->itineraryItems()->create([
        'day_number' => 1,
        'sort_order' => 1,
        'title' => ['en' => 'Day 1 EN', 'id' => 'Hari 1 ID'],
        'description' => ['en' => 'Desc EN', 'id' => 'Desc ID'],
    ]);

    // Test Items (Include/Exclude)
    $item = $destination->includeItems()->create([
        'type' => 'include',
        'sort_order' => 1,
        'label' => ['en' => 'Includes EN', 'id' => 'Includes ID'],
    ]);

    // Test FAQ
    $faq = $destination->faqs()->create([
        'sort_order' => 1,
        'question' => ['en' => 'Q EN', 'id' => 'Q ID'],
        'answer' => ['en' => 'A EN', 'id' => 'A ID'],
    ]);

    // Test Trip Info
    $tripInfo = $destination->tripInfos()->create([
        'sort_order' => 1,
        'icon' => 'info',
        'label' => ['en' => 'Label EN', 'id' => 'Label ID'],
        'value' => ['en' => 'Value EN', 'id' => 'Value ID'],
    ]);

    App::setLocale('en');
    expect($itinerary->title)->toBe('Day 1 EN');
    expect($item->label)->toBe('Includes EN');
    expect($faq->question)->toBe('Q EN');
    expect($tripInfo->label)->toBe('Label EN');

    App::setLocale('id');
    expect($itinerary->title)->toBe('Hari 1 ID');
    expect($item->label)->toBe('Includes ID');
    expect($faq->question)->toBe('Q ID');
    expect($tripInfo->label)->toBe('Label ID');
});
