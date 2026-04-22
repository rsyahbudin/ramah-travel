<?php

use App\Models\Page;
use App\Models\PageSection;
use App\Models\Language;

beforeEach(function () {
    if (Language::count() === 0) {
        Language::create(['code' => 'en', 'name' => 'English', 'is_active' => true]);
        Language::create(['code' => 'id', 'name' => 'Indonesian', 'is_active' => true]);
    }
});

test('updating one language in meta does not overwrite others', function () {
    $page = Page::create(['slug' => 'test-meta', 'title' => ['en' => 'Test']]);
    $section = $page->sections()->create([
        'key' => 'test_section',
        'type' => 'text',
        'meta' => [
            'cta_text' => [
                'en' => 'English CTA',
                'id' => 'Indonesian CTA'
            ]
        ]
    ]);

    // Update only Indonesian
    $section->updateTranslatedMeta([
        'cta_text' => ['id' => 'New Indonesian CTA']
    ]);
    $section->save();

    $section->refresh();
    
    expect($section->meta['cta_text']['id'])->toBe('New Indonesian CTA');
    expect($section->meta['cta_text']['en'])->toBe('English CTA');
});

test('getTranslatedMeta handles fallbacks correctly', function () {
    $page = Page::create(['slug' => 'test-fallback', 'title' => ['en' => 'Test']]);
    $section = $page->sections()->create([
        'key' => 'test_section_fallback',
        'type' => 'text',
        'meta' => [
            'cta_text' => [
                'en' => 'Default EN'
            ]
        ]
    ]);

    app()->setLocale('id');
    // Indonesian is missing, should fallback to EN
    expect($section->getTranslatedMeta('cta_text'))->toBe('Default EN');

    // Add Indonesian
    $section->updateTranslatedMeta(['cta_text' => ['id' => 'Indonesian text']]);
    $section->save();

    expect($section->getTranslatedMeta('cta_text'))->toBe('Indonesian text');
});
