<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Language;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        Language::all(); // Ensure languages are loaded

        $destinations = [
            [
                'slug' => 'sacred-valleys-ubud',
                'price' => 1200,
                'is_featured' => true,
                'is_visible' => true,
                'title' => [
                    'en' => 'Sacred Valleys of Ubud',
                    'id' => 'Lembah Suci Ubud',
                    'es' => 'Valles Sagrados de Ubud',
                ],
                'description' => [
                    'en' => 'An immersion into the spiritual heart of Bali.',
                    'id' => 'Perendaman ke jantung spiritual Bali.',
                    'es' => 'Una inmersión en el corazón espiritual de Bali.',
                ],
                'location' => [
                    'en' => 'Ubud, Bali',
                    'id' => 'Ubud, Bali',
                    'es' => 'Ubud, Bali',
                ],
                'duration' => [
                    'en' => '4 Days',
                    'id' => '4 Hari',
                    'es' => '4 Días',
                ],
                'theme' => [
                    'en' => 'Spiritual / Culture',
                    'id' => 'Spiritual / Budaya',
                    'es' => 'Espiritual / Cultura',
                ],
                'highlights' => [
                    'en' => "• Private temple blessing\n• Gourmet dining over the valley\n• Artisan masterclasses",
                    'id' => "• Pemberkatan pura pribadi\n• Makan malam mewah di atas lembah\n• Kelas master pengrajin",
                    'es' => "• Bendición en templo privado\n• Cena gourmet sobre el valle\n• Clases magistrales de artesanos",
                ],
                'itinerary' => [
                    [
                        'day_number' => 1,
                        'sort_order' => 1,
                        'title' => ['en' => 'Arrival', 'id' => 'Kedatangan', 'es' => 'Llegada'],
                        'description' => ['en' => 'Arrival and check-in at your luxury villa.', 'id' => 'Tiba dan lapor masuk di villa mewah Anda.', 'es' => 'Llegada y registro en su villa de lujo.'],
                    ],
                ],
                'includes' => [
                    [
                        'sort_order' => 1,
                        'label' => ['en' => 'Luxury Villa', 'id' => 'Villa Mewah', 'es' => 'Villa de Lujo'],
                    ],
                    [
                        'sort_order' => 2,
                        'label' => ['en' => 'Private Guide', 'id' => 'Pemandu Pribadi', 'es' => 'Guía Privado'],
                    ],
                ],
                'excludes' => [
                    [
                        'sort_order' => 1,
                        'label' => ['en' => 'Flights', 'id' => 'Tiket Pesawat', 'es' => 'Vuelos'],
                    ],
                    [
                        'sort_order' => 2,
                        'label' => ['en' => 'Personal expenses', 'id' => 'Pengeluaran Pribadi', 'es' => 'Gastos personales'],
                    ],
                ],
            ],
            [
                'slug' => 'komodo-expedition',
                'price' => 2500,
                'is_featured' => true,
                'is_visible' => true,
                'title' => [
                    'en' => 'Komodo Expedition',
                    'id' => 'Ekspedisi Komodo',
                    'es' => 'Expedición Komodo',
                ],
                'description' => [
                    'en' => 'Sail through prehistoric landscapes.',
                    'id' => 'Berlayar melintasi lanskap prasejarah.',
                    'es' => 'Navega a través de paisajes prehistóricos.',
                ],
                'location' => [
                    'en' => 'Labuan Bajo',
                    'id' => 'Labuan Bajo',
                    'es' => 'Labuan Bajo',
                ],
                'duration' => [
                    'en' => '3 Days',
                    'id' => '3 Hari',
                    'es' => '3 Días',
                ],
                'theme' => [
                    'en' => 'Adventure / Wildlife',
                    'id' => 'Petualangan / Satwa Liar',
                    'es' => 'Aventura / Vida Silvestre',
                ],
                'highlights' => [
                    'en' => "• Private Yacht\n• Dragon trekking\n• Pink Beach picnic",
                    'id' => "• Yacht Pribadi\n• Trekking Komodo\n• Piknik di Pantai Merah Muda",
                    'es' => "• Yate Privado\n• Trekking de dragones\n• Picnic en la Playa Rosa",
                ],
                'itinerary' => [
                    [
                        'day_number' => 1,
                        'sort_order' => 1,
                        'title' => ['en' => 'Sailing', 'id' => 'Berlayar', 'es' => 'Navegación'],
                        'description' => ['en' => 'Explore the stunning archipelago.', 'id' => 'Jelajahi kepulauan yang memukau.', 'es' => 'Explora el impresionante archipiélago.'],
                    ],
                ],
                'includes' => [
                    [
                        'sort_order' => 1,
                        'label' => ['en' => 'Full Board', 'id' => 'Makan Penuh', 'es' => 'Pensión Completa'],
                    ],
                    [
                        'sort_order' => 2,
                        'label' => ['en' => 'Diving Gear', 'id' => 'Peralatan Menyelam', 'es' => 'Equipo de Buceo'],
                    ],
                ],
                'excludes' => [
                    [
                        'sort_order' => 1,
                        'label' => ['en' => 'Crew Tips', 'id' => 'Tip Kru', 'es' => 'Propinas a la tripulación'],
                    ],
                ],
            ],
        ];

        foreach ($destinations as $data) {
            $destination = Destination::updateOrCreate(
                ['slug' => $data['slug']],
                collect($data)->except(['itinerary', 'includes', 'excludes'])->toArray()
            );

            // Sync itinerary
            $destination->itineraryItems()->delete();
            foreach ($data['itinerary'] ?? [] as $item) {
                $destination->itineraryItems()->create($item);
            }

            // Sync includes
            $destination->includeItems()->delete();
            foreach ($data['includes'] ?? [] as $item) {
                $destination->includeItems()->create(array_merge($item, ['type' => 'include']));
            }

            // Sync excludes
            $destination->excludeItems()->delete();
            foreach ($data['excludes'] ?? [] as $item) {
                $destination->excludeItems()->create(array_merge($item, ['type' => 'exclude']));
            }
        }
    }
}
