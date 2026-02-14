<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Destination;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        Page::create([
            'slug' => 'about',
            'title' => 'About Us',
            'content' => "Welcome to TravelApp!\n\nWe are dedicated to providing the best travel experiences...",
        ]);

        Setting::create(['key' => 'whatsapp_number', 'value' => '628123456789']);
        Setting::create(['key' => 'admin_email', 'value' => 'admin@example.com']);

        Destination::create([
             'title' => 'Beautiful Bali',
             'slug' => 'beautiful-bali',
             'description' => 'Experience the magic of the Island of the Gods.',
             'price' => 500.00,
             'location' => 'Bali, Indonesia',
             'is_featured' => true,
        ]);
        
        Destination::create([
             'title' => 'Majestic Paris',
             'slug' => 'majestic-paris',
             'description' => 'The city of lights awaits you.',
             'price' => 1200.00,
             'location' => 'Paris, France',
             'is_featured' => true,
        ]);
    }
}
