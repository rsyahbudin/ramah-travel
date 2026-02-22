<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('authenticated users can view general settings', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('admin.settings'));
    $response->assertOk();
    $response->assertSee('General Settings');
    $response->assertSee('Branding');
    $response->assertSee('Contact Information');

    // Verify redundant sections are NOT seen
    $response->assertDontSee('Hero Section');
    $response->assertDontSee('Experience Tiers');
    $response->assertDontSee('Home CTA Section');
});

test('settings can be saved', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Volt::test('admin.settings')
        ->set('site_name', 'Updated Travel Name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('settings-saved');

    $this->assertDatabaseHas('settings', [
        'key' => 'site_name',
        'value' => 'Updated Travel Name',
    ]);
});
