<?php

use App\Models\Destination;
use App\Models\User;

use function Pest\Laravel\get;

it('does not show dashboard link in public navbar for guests', function () {
    get(route('home'))
        ->assertOk()
        ->assertDontSee('Dashboard');
});

it('does not show dashboard link in public navbar for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertDontSee(route('admin.dashboard'));
});

it('can create a destination with new detail fields', function () {
    $destination = Destination::create([
        'title' => 'Komodo Island',
        'slug' => 'komodo-island',
        'description' => 'A beautiful island.',
        'price' => 1500.00,
        'location' => 'Labuan Bajo',
        'person' => 4,
        'itinerary' => [
            ['day' => 'Day 1', 'activity' => 'Arrival & Hotel Check-in'],
            ['day' => 'Day 2', 'activity' => 'Komodo National Park Tour'],
        ],
        'includes' => ['Airport pickup & drop-off', 'Hotel accommodation'],
        'excludes' => ['International flights', 'Travel insurance'],
        'faq' => [
            ['question' => 'What to bring?', 'answer' => 'Sunscreen and comfortable shoes.'],
        ],
        'trip_info' => [
            ['key' => 'Wifi', 'value' => 'Yes'],
            ['key' => 'Transportation', 'value' => 'Car'],
        ],
    ]);

    $destination->refresh();

    expect($destination->person)->toBe(4);
    expect($destination->itinerary)->toHaveCount(2);
    expect($destination->itinerary[0]['day'])->toBe('Day 1');
    expect($destination->includes)->toHaveCount(2);
    expect($destination->excludes)->toHaveCount(2);
    expect($destination->faq)->toHaveCount(1);
    expect($destination->faq[0]['question'])->toBe('What to bring?');
    expect($destination->trip_info)->toHaveCount(2);
    expect($destination->trip_info[0]['key'])->toBe('Wifi');
});

it('shows detail fields on public destination page', function () {
    $destination = Destination::create([
        'title' => 'Bali Paradise',
        'slug' => 'bali-paradise',
        'description' => 'A tropical paradise.',
        'price' => 2000.00,
        'location' => 'Bali',
        'person' => 2,
        'itinerary' => [
            ['day' => 'Day 1', 'activity' => 'Beach day and sunset dinner'],
        ],
        'includes' => ['Hotel stay', 'Breakfast'],
        'excludes' => ['Domestic flights'],
        'faq' => [
            ['question' => 'Is it pet friendly?', 'answer' => 'No pets allowed.'],
        ],
        'trip_info' => [
            ['key' => 'Free Cancelation', 'value' => 'Yes'],
        ],
    ]);

    get(route('destinations.show', $destination))
        ->assertOk()
        ->assertSee('2 Pax')
        ->assertSee('Day 1')
        ->assertSee('Beach day and sunset dinner')
        ->assertSee('Hotel stay')
        ->assertSee('Domestic flights')
        ->assertSee('Is it pet friendly?')
        ->assertSee('No pets allowed.')
        ->assertSee('Free Cancelation');
});

it('shows destination page without detail fields when they are null', function () {
    $destination = Destination::create([
        'title' => 'Simple Trip',
        'slug' => 'simple-trip',
        'description' => 'A simple trip without extra details.',
        'price' => 500.00,
        'location' => 'Jakarta',
    ]);

    get(route('destinations.show', $destination))
        ->assertOk()
        ->assertSee('Simple Trip')
        ->assertSee('A simple trip without extra details.');
});
