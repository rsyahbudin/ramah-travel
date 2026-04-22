<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = Language::all()->keyBy('code');

        $pages = [
            [
                'slug' => 'home',
                'image_path' => null,
                'translations' => [
                    'en' => ['title' => 'Home'],
                    'id' => ['title' => 'Beranda'],
                    'es' => ['title' => 'Inicio'],
                ],
                'sections' => [
                    [
                        'type' => 'hero',
                        'key' => 'home_hero',
                        'sort_order' => 1,
                        'is_visible' => true,
                        'meta' => ['label' => ['en' => 'The Future of Exploration', 'id' => 'Masa Depan Eksplorasi', 'es' => 'El Futuro de la Exploración'], 'cta_text' => ['en' => 'Discover More', 'id' => 'Jelajahi Lebih Lanjut', 'es' => 'Descubrir más']],
                        'translations' => [
                            'en' => ['heading' => "Redefining the\nArt of Travel.", 'body' => "Experience the world's most secluded corners through a lens of absolute luxury and curated exclusivity."],
                            'id' => ['heading' => "Mendefinisikan Ulang\nSeni Perjalanan.", 'body' => 'Nikmati sudut-sudut paling terpencil di dunia melalui lensa kemewahan mutlak dan eksklusivitas yang terkurasi.'],
                            'es' => ['heading' => "Redefiniendo el\nArte de Viajar.", 'body' => 'Experimente los rincones más recónditos del mundo a través de una lente de lujo absoluto y exclusividad curada.'],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'key' => 'home_about',
                        'sort_order' => 2,
                        'is_visible' => true,
                        'meta' => [
                            'label' => ['en' => 'Since 2008', 'id' => 'Sejak 2008', 'es' => 'Desde 2008'],
                            'stat_number' => ['en' => '15+', 'id' => '15+', 'es' => '15+'],
                            'stat_text' => ['en' => 'Years of Crafting Bespoke Experiences', 'id' => 'Tahun Menciptakan Pengalaman Kustom', 'es' => 'Años creando experiencias a medida'],
                        ],
                        'translations' => [
                            'en' => ['heading' => "The Journey Behind\nOur Legacy.", 'body' => 'Founded on the principle that travel should be as unique as the traveler. We curate experiences that transcend the ordinary.'],
                            'id' => ['heading' => "Perjalanan di Balik\nWarisan Kami.", 'body' => 'Didirikan berdasarkan prinsip bahwa perjalanan harus seunik pelancongnya. Kami mengkurasi pengalaman yang melampaui kebiasaan.'],
                            'es' => ['heading' => "El viaje detrás de\nnuestro legado.", 'body' => 'Fundado en el principio de que los viajes deben ser tan únicos como el viajero. Curamos experiencias que trascienden lo ordinario.'],
                        ],
                    ],
                    [
                        'type' => 'features',
                        'key' => 'home_experience_tiers',
                        'sort_order' => 3,
                        'is_visible' => true,
                        'meta' => ['label' => ['en' => 'Tailored For You', 'id' => 'Disesuaikan Untuk Anda', 'es' => 'A tu medida']],
                        'translations' => [
                            'en' => ['heading' => 'How We Travel', 'body' => ''],
                            'id' => ['heading' => 'Cara Kami Bepergian', 'body' => ''],
                            'es' => ['heading' => 'Cómo viajamos', 'body' => ''],
                        ],
                        'features' => [
                            [
                                'icon' => 'diamond',
                                'sort_order' => 1,
                                'translations' => [
                                    'en' => ['title' => 'Elite Concierge', 'description' => '24/7 dedicated support for every whim.'],
                                    'id' => ['title' => 'Pramutamu Elit', 'description' => 'Dukungan khusus 24/7 untuk setiap keinginan.'],
                                    'es' => ['title' => 'Conserje Élite', 'description' => 'Soporte dedicado 24/7 para cada capricho.'],
                                ],
                            ],
                            [
                                'icon' => 'map',
                                'sort_order' => 2,
                                'translations' => [
                                    'en' => ['title' => 'Bespoke Itineraries', 'description' => 'Every journey is custom-built from the ground up.'],
                                    'id' => ['title' => 'Itinerary Bertujuan Khusus', 'description' => 'Setiap perjalanan dibangun khusus dari awal.'],
                                    'es' => ['title' => 'Itinerarios a Medida', 'description' => 'Cada viaje se construye a medida desde cero.'],
                                ],
                            ],
                            [
                                'icon' => 'verified_user',
                                'sort_order' => 3,
                                'translations' => [
                                    'en' => ['title' => 'Insider Access', 'description' => 'Gain entry to private estates and hidden gems.'],
                                    'id' => ['title' => 'Akses Orang Dalam', 'description' => 'Dapatkan akses ke perkebunan pribadi dan permata tersembunyi.'],
                                    'es' => ['title' => 'Acceso Privilegiado', 'description' => 'Obtenga acceso a fincas privadas y gemas ocultas.'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'key' => 'home_destination',
                        'sort_order' => 4,
                        'is_visible' => true,
                        'meta' => ['label' => ['en' => 'Curated Selection', 'id' => 'Pilihan Terkurasi', 'es' => 'Selección Curada']],
                        'translations' => [
                            'en' => ['heading' => 'Destinations Spotlight', 'body' => ''],
                            'id' => ['heading' => 'Sorotan Destinasi', 'body' => ''],
                            'es' => ['heading' => 'Destinos en Foco', 'body' => ''],
                        ],
                    ],
                    [
                        'type' => 'cta',
                        'key' => 'home_cta',
                        'sort_order' => 5,
                        'is_visible' => true,
                        'meta' => [],
                        'translations' => [
                            'en' => ['heading' => 'Stay Inspired.', 'body' => 'Join our inner circle for exclusive updates and private travel insights.'],
                            'id' => ['heading' => 'Tetap Terinspirasi.', 'body' => 'Bergabunglah dengan lingkaran dalam kami untuk pembaruan eksklusif dan wawasan perjalanan pribadi.'],
                            'es' => ['heading' => 'Mantente inspirado.', 'body' => 'Únase a nuestro círculo íntimo para recibir actualizaciones exclusivas y conocimientos de viajes privados.'],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'about',
                'image_path' => null,
                'translations' => [
                    'en' => ['title' => 'The Journey Behind Our Legacy.'],
                    'id' => ['title' => 'Perjalanan di Balik Warisan Kami.'],
                    'es' => ['title' => 'El viaje detrás de nuestro legado.'],
                ],
                'sections' => [
                    [
                        'type' => 'hero',
                        'key' => 'about_hero',
                        'sort_order' => 1,
                        'is_visible' => true,
                        'meta' => ['label' => ['en' => 'Our Story', 'id' => 'Cerita Kami', 'es' => 'Nuestra historia']],
                        'translations' => [
                            'en' => ['heading' => 'The Journey Behind Our Legacy.', 'body' => 'The journey behind our legacy and the passion that drives us.'],
                            'id' => ['heading' => 'Perjalanan di Balik Warisan Kami.', 'body' => 'Perjalanan di balik warisan kami dan semangat yang menggerakkan kami.'],
                            'es' => ['heading' => 'El viaje detrás de nuestro legado.', 'body' => 'El viaje tras nuestro legado y la pasión que nos impulsa.'],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'key' => 'about_who_we_are',
                        'sort_order' => 2,
                        'is_visible' => true,
                        'meta' => ['label' => ['en' => 'Who We Are', 'id' => 'Siapa Kami', 'es' => 'Quiénes somos']],
                        'translations' => [
                            'en' => [
                                'heading' => 'Who We Are',
                                'body' => "Founded on the principle that travel should be as unique as the traveler, Ramah Indonesia has been curating extraordinary journeys since 2008. We believe in the power of exploration to transform lives, and we dedicate ourselves to uncovering the world's most secluded corners through a lens of absolute luxury and curated exclusivity.",
                            ],
                            'id' => [
                                'heading' => 'Siapa Kami',
                                'body' => 'Didirikan berdasarkan prinsip bahwa perjalanan harus seunik pelancongnya, Ramah Indonesia telah mengkurasi perjalanan luar biasa sejak 2008. Kami percaya pada kekuatan eksplorasi untuk mengubah hidup, dan kami mendedikasikan diri untuk mengungkap sudut-sudut paling terpencil di dunia melalui lensa kemewahan mutlak dan eksklusivitas yang terkurasi.',
                            ],
                            'es' => [
                                'heading' => 'Quiénes somos',
                                'body' => 'Fundada según el principio de que viajar debe ser tan único como el viajero, Ramah Indonesia ha sido curando viajes extraordinarios desde 2008. Creemos en el poder de la exploración para transformar vidas y nos dedicamos a descubrir los rincones más recónditos del mundo a través de una lente de lujo absoluto y exclusividad curada.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'destinations',
                'image_path' => null,
                'translations' => [
                    'en' => ['title' => 'Our Destinations'],
                    'id' => ['title' => 'Destinasi Kami'],
                    'es' => ['title' => 'Nuestros Destinos'],
                ],
                'sections' => [
                    [
                        'type' => 'hero',
                        'key' => 'destinations_hero',
                        'sort_order' => 1,
                        'is_visible' => true,
                        'meta' => ['label' => ['en' => 'Curated Selection', 'id' => 'Pilihan Terkurasi', 'es' => 'Selección curada']],
                        'translations' => [
                            'en' => ['heading' => 'Our Destinations', 'body' => 'Discover handpicked journeys crafted for the world\'s most discerning travelers.'],
                            'id' => ['heading' => 'Destinasi Kami', 'body' => 'Temukan perjalanan pilihan yang dirancang untuk para pelancong paling istimewa di dunia.'],
                            'es' => ['heading' => 'Nuestros Destinos', 'body' => 'Descubra viajes seleccionados diseñados para los viajeros más exigentes del mundo.'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($pages as $data) {
            $page = Page::updateOrCreate(
                ['slug' => $data['slug']],
                ['image_path' => $data['image_path']]
            );

            // Sync page translations
            foreach ($data['translations'] as $code => $fields) {
                if (! isset($languages[$code])) {
                    continue;
                }

                $page->translations()->updateOrCreate(
                    ['language_id' => $languages[$code]->id],
                    $fields
                );
            }

            // Sync sections
            foreach ($data['sections'] as $sectionData) {
                $section = PageSection::updateOrCreate(
                    ['page_id' => $page->id, 'key' => $sectionData['key']],
                    [
                        'type' => $sectionData['type'],
                        'sort_order' => $sectionData['sort_order'],
                        'is_visible' => $sectionData['is_visible'],
                        'meta' => $sectionData['meta'],
                    ]
                );

                foreach ($sectionData['translations'] as $code => $fields) {
                    if (! isset($languages[$code])) {
                        continue;
                    }

                    $section->translations()->updateOrCreate(
                        ['language_id' => $languages[$code]->id],
                        $fields
                    );
                }

                if (isset($sectionData['features'])) {
                    foreach ($sectionData['features'] as $featureData) {
                        $feature = $section->features()->updateOrCreate(
                            ['icon' => $featureData['icon']],
                            ['sort_order' => $featureData['sort_order']]
                        );

                        foreach ($featureData['translations'] as $code => $fields) {
                            if (! isset($languages[$code])) {
                                continue;
                            }

                            $feature->translations()->updateOrCreate(
                                ['language_id' => $languages[$code]->id],
                                $fields
                            );
                        }
                    }
                }
            }
        }
    }
}
