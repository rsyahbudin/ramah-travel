<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = [
            [
                'title' => [
                    'en' => 'Sacred Valleys of Ubud',
                    'id' => 'Lembah Suci Ubud',
                    'es' => 'Valles Sagrados de Ubud',
                ],
                'slug' => 'sacred-valleys-ubud',
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
                'price' => 1200,
                'is_featured' => true,
                'is_visible' => true,
                'theme' => [
                    'en' => 'Spiritual / Culture',
                    'id' => 'Spiritual / Budaya',
                    'es' => 'Espiritual / Cultura',
                ],
                'highlights' => [
                    'en' => ['Private temple blessing', 'Gourmet dining over the valley', 'Artisan masterclasses'],
                    'id' => ['Pemberkatan pura pribadi', 'Makan malam mewah di atas lembah', 'Kelas master pengrajin'],
                    'es' => ['Bendición en templo privado', 'Cena gourmet sobre el valle', 'Clases magistrales de artesanos'],
                ],
                'itinerary' => [
                    'en' => [
                        ['day' => 'Day 1', 'title' => 'Arrival', 'description' => 'Arrival and check-in.'],
                    ],
                    'id' => [
                        ['day' => 'Hari 1', 'title' => 'Kedatangan', 'description' => 'Tiba dan lapor masuk.'],
                    ],
                    'es' => [
                        ['day' => 'Día 1', 'title' => 'Llegada', 'description' => 'Llegada y registro.'],
                    ],
                ],
                'includes' => [
                    'en' => ['Luxury Villa', 'Private Guide'],
                    'id' => ['Villa Mewah', 'Pemandu Pribadi'],
                    'es' => ['Villa de Lujo', 'Guía Privado'],
                ],
                'excludes' => [
                    'en' => ['Flights', 'Personal expenses'],
                    'id' => ['Tiket Pesawat', 'Pengeluaran Pribadi'],
                    'es' => ['Vuelos', 'Gastos personales'],
                ],
            ],
            [
                'title' => [
                    'en' => 'Komodo Expedition',
                    'id' => 'Ekspedisi Komodo',
                    'es' => 'Expedición Komodo',
                ],
                'slug' => 'komodo-expedition',
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
                'price' => 2500,
                'is_featured' => true,
                'is_visible' => true,
                'theme' => [
                    'en' => 'Adventure / Wildlife',
                    'id' => 'Petualangan / Satwa Liar',
                    'es' => 'Aventura / Vida Silvestre',
                ],
                'highlights' => [
                    'en' => ['Private Yacht', 'Dragon trekking', 'Pink Beach picnic'],
                    'id' => ['Yacht Pribadi', 'Trekking Komodo', 'Piknik di Pantai Merah Muda'],
                    'es' => ['Yate Privado', 'Trekking de dragones', 'Picnic en la Playa Rosa'],
                ],
                'itinerary' => [
                    'en' => [['day' => 'Day 1', 'title' => 'Sailing', 'description' => 'Explore the archipelago.']],
                    'id' => [['day' => 'Hari 1', 'title' => 'Berlayar', 'description' => 'Jelajahi kepulauan.']],
                    'es' => [['day' => 'Día 1', 'title' => 'Navegación', 'description' => 'Explora el archipiélago.']],
                ],
                'includes' => [
                    'en' => ['Full Board', 'Diving Gear'],
                    'id' => ['Makan Penuh', 'Peralatan Menyelam'],
                    'es' => ['Pensión Completa', 'Equipo de Buceo'],
                ],
                'excludes' => [
                    'en' => ['Crew Tips'],
                    'id' => ['Tip Kru'],
                    'es' => ['Propinas a la tripulación'],
                ],
            ],
        ];

        foreach ($destinations as $data) {
            // Encode translatable fields that are NOT in $casts
            $translatableNotCast = ['title', 'description', 'location', 'duration', 'theme'];
            foreach ($translatableNotCast as $field) {
                if (isset($data[$field])) {
                    $data[$field] = json_encode($data[$field]);
                }
            }
            Destination::create($data);
        }
    }
}
