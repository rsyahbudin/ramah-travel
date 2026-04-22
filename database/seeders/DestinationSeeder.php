<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Language;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $languages = Language::all()->keyBy('code');

        $destinations = [
            [
                'slug' => 'sacred-valleys-ubud',
                'price' => 1200,
                'is_featured' => true,
                'is_visible' => true,
                'translations' => [
                    'en' => [
                        'title' => 'Sacred Valleys of Ubud',
                        'description' => 'An immersion into the spiritual heart of Bali.',
                        'location' => 'Ubud, Bali',
                        'duration' => '4 Days',
                        'theme' => 'Spiritual / Culture',
                        'highlights' => "• Private temple blessing\n• Gourmet dining over the valley\n• Artisan masterclasses",
                    ],
                    'id' => [
                        'title' => 'Lembah Suci Ubud',
                        'description' => 'Perendaman ke jantung spiritual Bali.',
                        'location' => 'Ubud, Bali',
                        'duration' => '4 Hari',
                        'theme' => 'Spiritual / Budaya',
                        'highlights' => "• Pemberkatan pura pribadi\n• Makan malam mewah di atas lembah\n• Kelas master pengrajin",
                    ],
                    'es' => [
                        'title' => 'Valles Sagrados de Ubud',
                        'description' => 'Una inmersión en el corazón espiritual de Bali.',
                        'location' => 'Ubud, Bali',
                        'duration' => '4 Días',
                        'theme' => 'Espiritual / Cultura',
                        'highlights' => "• Bendición en templo privado\n• Cena gourmet sobre el valle\n• Clases magistrales de artesanos",
                    ],
                ],
                'itinerary' => [
                    [
                        'day_number' => 1,
                        'sort_order' => 1,
                        'translations' => [
                            'en' => ['title' => 'Arrival', 'description' => 'Arrival and check-in at your luxury villa.'],
                            'id' => ['title' => 'Kedatangan', 'description' => 'Tiba dan lapor masuk di villa mewah Anda.'],
                            'es' => ['title' => 'Llegada', 'description' => 'Llegada y registro en su villa de lujo.'],
                        ],
                    ],
                ],
                'includes' => [
                    [
                        'sort_order' => 1,
                        'translations' => [
                            'en' => ['label' => 'Luxury Villa'],
                            'id' => ['label' => 'Villa Mewah'],
                            'es' => ['label' => 'Villa de Lujo'],
                        ],
                    ],
                    [
                        'sort_order' => 2,
                        'translations' => [
                            'en' => ['label' => 'Private Guide'],
                            'id' => ['label' => 'Pemandu Pribadi'],
                            'es' => ['label' => 'Guía Privado'],
                        ],
                    ],
                ],
                'excludes' => [
                    [
                        'sort_order' => 1,
                        'translations' => [
                            'en' => ['label' => 'Flights'],
                            'id' => ['label' => 'Tiket Pesawat'],
                            'es' => ['label' => 'Vuelos'],
                        ],
                    ],
                    [
                        'sort_order' => 2,
                        'translations' => [
                            'en' => ['label' => 'Personal expenses'],
                            'id' => ['label' => 'Pengeluaran Pribadi'],
                            'es' => ['label' => 'Gastos personales'],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'komodo-expedition',
                'price' => 2500,
                'is_featured' => true,
                'is_visible' => true,
                'translations' => [
                    'en' => [
                        'title' => 'Komodo Expedition',
                        'description' => 'Sail through prehistoric landscapes.',
                        'location' => 'Labuan Bajo',
                        'duration' => '3 Days',
                        'theme' => 'Adventure / Wildlife',
                        'highlights' => "• Private Yacht\n• Dragon trekking\n• Pink Beach picnic",
                    ],
                    'id' => [
                        'title' => 'Ekspedisi Komodo',
                        'description' => 'Berlayar melintasi lanskap prasejarah.',
                        'location' => 'Labuan Bajo',
                        'duration' => '3 Hari',
                        'theme' => 'Petualangan / Satwa Liar',
                        'highlights' => "• Yacht Pribadi\n• Trekking Komodo\n• Piknik di Pantai Merah Muda",
                    ],
                    'es' => [
                        'title' => 'Expedición Komodo',
                        'description' => 'Navega a través de paisajes prehistóricos.',
                        'location' => 'Labuan Bajo',
                        'duration' => '3 Días',
                        'theme' => 'Aventura / Vida Silvestre',
                        'highlights' => "• Yate Privado\n• Trekking de dragones\n• Picnic en la Playa Rosa",
                    ],
                ],
                'itinerary' => [
                    [
                        'day_number' => 1,
                        'sort_order' => 1,
                        'translations' => [
                            'en' => ['title' => 'Sailing', 'description' => 'Explore the stunning archipelago.'],
                            'id' => ['title' => 'Berlayar', 'description' => 'Jelajahi kepulauan yang memukau.'],
                            'es' => ['title' => 'Navegación', 'description' => 'Explora el impresionante archipiélago.'],
                        ],
                    ],
                ],
                'includes' => [
                    [
                        'sort_order' => 1,
                        'translations' => [
                            'en' => ['label' => 'Full Board'],
                            'id' => ['label' => 'Makan Penuh'],
                            'es' => ['label' => 'Pensión Completa'],
                        ],
                    ],
                    [
                        'sort_order' => 2,
                        'translations' => [
                            'en' => ['label' => 'Diving Gear'],
                            'id' => ['label' => 'Peralatan Menyelam'],
                            'es' => ['label' => 'Equipo de Buceo'],
                        ],
                    ],
                ],
                'excludes' => [
                    [
                        'sort_order' => 1,
                        'translations' => [
                            'en' => ['label' => 'Crew Tips'],
                            'id' => ['label' => 'Tip Kru'],
                            'es' => ['label' => 'Propinas a la tripulación'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($destinations as $data) {
            $destination = Destination::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'price' => $data['price'],
                    'is_featured' => $data['is_featured'],
                    'is_visible' => $data['is_visible'],
                ]
            );

            // Sync translations
            foreach ($data['translations'] as $code => $fields) {
                if (! isset($languages[$code])) {
                    continue;
                }

                $destination->translations()->updateOrCreate(
                    ['language_id' => $languages[$code]->id],
                    $fields
                );
            }

            // Sync itinerary
            $destination->itineraryItems()->delete();
            foreach ($data['itinerary'] ?? [] as $item) {
                $itineraryItem = $destination->itineraryItems()->create([
                    'day_number' => $item['day_number'],
                    'sort_order' => $item['sort_order'],
                ]);

                foreach ($item['translations'] as $code => $fields) {
                    if (! isset($languages[$code])) {
                        continue;
                    }

                    $itineraryItem->translations()->updateOrCreate(
                        ['language_id' => $languages[$code]->id],
                        $fields
                    );
                }
            }

            // Sync includes
            $destination->includeItems()->delete();
            foreach ($data['includes'] ?? [] as $item) {
                $includeItem = $destination->includeItems()->create([
                    'type' => 'include',
                    'sort_order' => $item['sort_order'],
                ]);

                foreach ($item['translations'] as $code => $fields) {
                    if (! isset($languages[$code])) {
                        continue;
                    }

                    $includeItem->translations()->updateOrCreate(
                        ['language_id' => $languages[$code]->id],
                        $fields
                    );
                }
            }

            // Sync excludes
            $destination->excludeItems()->delete();
            foreach ($data['excludes'] ?? [] as $item) {
                $excludeItem = $destination->excludeItems()->create([
                    'type' => 'exclude',
                    'sort_order' => $item['sort_order'],
                ]);

                foreach ($item['translations'] as $code => $fields) {
                    if (! isset($languages[$code])) {
                        continue;
                    }

                    $excludeItem->translations()->updateOrCreate(
                        ['language_id' => $languages[$code]->id],
                        $fields
                    );
                }
            }
        }
    }
}
