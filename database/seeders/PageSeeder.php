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
        Language::all(); // Ensure languages are present

        $pages = [
            [
                'slug' => 'home',
                'image_path' => null,
                'title' => [
                    'en' => 'Home',
                    'id' => 'Beranda',
                    'es' => 'Inicio',
                ],
                'sections' => [
                    [
                        'type' => 'hero',
                        'key' => 'home_hero',
                        'sort_order' => 1,
                        'is_visible' => true,
                        'meta' => [
                            'label' => ['en' => 'The Future of Exploration', 'id' => 'Masa Depan Eksplorasi', 'es' => 'El Futuro de la Exploración'],
                            'cta_text' => ['en' => 'Discover More', 'id' => 'Jelajahi Lebih Lanjut', 'es' => 'Descubrir más'],
                            'cta_secondary_text' => ['en' => 'Our Story', 'id' => 'Cerita Kami', 'es' => 'Nuestra historia'],
                            'cta_link' => '/destinations',
                        ],
                        'title' => [
                            'en' => "Redefining the\nArt of Travel.",
                            'id' => "Mendefinisikan Ulang\nSeni Perjalanan.",
                            'es' => "Redefiniendo el\nArte de Viajar.",
                        ],
                        'content' => [
                            'en' => "Experience the world's most secluded corners through a lens of absolute luxury and curated exclusivity.",
                            'id' => 'Nikmati sudut-sudut paling terpencil di dunia melalui lensa kemewahan mutlak dan eksklusivitas yang terkurasi.',
                            'es' => 'Experimente los rincones más recónditos del mundo a través de una lente de lujo absoluto y exclusividad curada.',
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
                            'cta_text' => ['en' => 'Read Our Story', 'id' => 'Baca Cerita Kami', 'es' => 'Leer nuestra historia'],
                        ],
                        'title' => [
                            'en' => "The Journey Behind\nOur Legacy.",
                            'id' => "Perjalanan di Balik\nWarisan Kami.",
                            'es' => "El viaje detrás de\nnuestro legado.",
                        ],
                        'content' => [
                            'en' => 'Founded on the principle that travel should be as unique as the traveler. We curate experiences that transcend the ordinary.',
                            'id' => 'Didirikan berdasarkan prinsip bahwa perjalanan harus seunik pelancongnya. Kami mengkurasi pengalaman yang melampaui kebiasaan.',
                            'es' => 'Fundado en el principio de que los viajes deben ser tan únicos como el viajero. Curamos experiencias que trascienden lo ordinario.',
                        ],
                    ],
                    [
                        'type' => 'features',
                        'key' => 'home_experience_tiers',
                        'sort_order' => 3,
                        'is_visible' => true,
                        'meta' => ['label' => ['en' => 'Tailored For You', 'id' => 'Disesuaikan Untuk Anda', 'es' => 'A tu medida']],
                        'title' => [
                            'en' => 'How We Travel',
                            'id' => 'Cara Kami Bepergian',
                            'es' => 'Cómo viajamos',
                        ],
                        'content' => [
                            'en' => '',
                            'id' => '',
                            'es' => '',
                        ],
                        'features' => [
                            [
                                'icon' => 'diamond',
                                'sort_order' => 1,
                                'title' => [
                                    'en' => 'Elite Concierge',
                                    'id' => 'Pramutamu Elit',
                                    'es' => 'Conserje Élite',
                                ],
                                'description' => [
                                    'en' => '24/7 dedicated support for every whim.',
                                    'id' => 'Dukungan khusus 24/7 untuk setiap keinginan.',
                                    'es' => 'Soporte dedicado 24/7 para cada capricho.',
                                ],
                            ],
                            [
                                'icon' => 'map',
                                'sort_order' => 2,
                                'title' => [
                                    'en' => 'Bespoke Itineraries',
                                    'id' => 'Itinerary Bertujuan Khusus',
                                    'es' => 'Itinerarios a Medida',
                                ],
                                'description' => [
                                    'en' => 'Every journey is custom-built from the ground up.',
                                    'id' => 'Setiap perjalanan dibangun khusus dari awal.',
                                    'es' => 'Cada viaje se construye a medida desde cero.',
                                ],
                            ],
                            [
                                'icon' => 'verified_user',
                                'sort_order' => 3,
                                'title' => [
                                    'en' => 'Insider Access',
                                    'id' => 'Akses Orang Dalam',
                                    'es' => 'Acceso Privilegiado',
                                ],
                                'description' => [
                                    'en' => 'Gain entry to private estates and hidden gems.',
                                    'id' => 'Dapatkan akses ke perkebunan pribadi dan permata tersembunyi.',
                                    'es' => 'Obtenga acceso a fincas privadas y gemas ocultas.',
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
                        'title' => [
                            'en' => 'Destinations Spotlight',
                            'id' => 'Sorotan Destinasi',
                            'es' => 'Destinos en Foco',
                        ],
                        'content' => [
                            'en' => '',
                            'id' => '',
                            'es' => '',
                        ],
                    ],
                    [
                        'type' => 'cta',
                        'key' => 'home_cta',
                        'sort_order' => 5,
                        'is_visible' => true,
                        'meta' => [
                            'cta_primary_text' => ['en' => 'Explore Destinations', 'id' => 'Jelajahi Destinasi', 'es' => 'Explorar Destinos'],
                            'cta_secondary_text' => ['en' => 'Learn More', 'id' => 'Pelajari Lebih Lanjut', 'es' => 'Aprende Más'],
                        ],
                        'title' => [
                            'en' => 'Stay Inspired.',
                            'id' => 'Tetap Terinspirasi.',
                            'es' => 'Mantente inspirado.',
                        ],
                        'content' => [
                            'en' => 'Join our inner circle for exclusive updates and private travel insights.',
                            'id' => 'Bergabunglah dengan lingkaran dalam kami untuk pembaruan eksklusif dan wawasan perjalanan pribadi.',
                            'es' => 'Únase a nuestro círculo íntimo para recibir actualizaciones exclusivas y conocimientos de viajes privados.',
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'about',
                'image_path' => null,
                'title' => [
                    'en' => 'The Journey Behind Our Legacy.',
                    'id' => 'Perjalanan di Balik Warisan Kami.',
                    'es' => 'El viaje detrás de nuestro legado.',
                ],
                'sections' => [
                    [
                        'type' => 'hero',
                        'key' => 'about_hero',
                        'sort_order' => 1,
                        'is_visible' => true,
                        'meta' => ['label' => ['en' => 'Our Story', 'id' => 'Cerita Kami', 'es' => 'Nuestra historia']],
                        'title' => [
                            'en' => 'The Journey Behind Our Legacy.',
                            'id' => 'Perjalanan di Balik Warisan Kami.',
                            'es' => 'El viaje detrás de nuestro legado.',
                        ],
                        'content' => [
                            'en' => 'The journey behind our legacy and the passion that drives us.',
                            'id' => 'Perjalanan di balik warisan kami dan semangat yang menggerakkan kami.',
                            'es' => 'El viaje tras nuestro legado y la pasión que nos impulsa.',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'key' => 'about_who_we_are',
                        'sort_order' => 2,
                        'is_visible' => true,
                        'meta' => ['label' => ['en' => 'Who We Are', 'id' => 'Siapa Kami', 'es' => 'Quiénes somos']],
                        'title' => [
                            'en' => 'Who We Are',
                            'id' => 'Siapa Kami',
                            'es' => 'Quiénes somos',
                        ],
                        'content' => [
                            'en' => "Founded on the principle that travel should be as unique as the traveler, Ramah Indonesia has been curating extraordinary journeys since 2008. We believe in the power of exploration to transform lives, and we dedicate ourselves to uncovering the world's most secluded corners through a lens of absolute luxury and curated exclusivity.",
                            'id' => 'Didirikan berdasarkan prinsip bahwa perjalanan harus seunik pelancongnya, Ramah Indonesia telah mengkurasi perjalanan luar biasa sejak 2008. Kami percaya pada kekuatan eksplorasi untuk mengubah hidup, dan kami mendedikasikan diri untuk mengungkap sudut-sudut paling terpencil di dunia melalui lensa kemewahan mutlak dan eksklusivitas yang terkurasi.',
                            'es' => 'Fundada según el principio de que viajar debe ser tan único como el viajero, Ramah Indonesia ha sido curando viajes extraordinarios desde 2008. Creemos en el poder de la exploración para transformar vidas y nos dedicamos a descubrir los rincones más recónditos del mundo a través de una lente de lujo absoluto y exclusividad curada.',
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'destinations',
                'image_path' => null,
                'title' => [
                    'en' => 'Our Destinations',
                    'id' => 'Destinasi Kami',
                    'es' => 'Nuestros Destinos',
                ],
                'sections' => [
                    [
                        'type' => 'hero',
                        'key' => 'destinations_hero',
                        'sort_order' => 1,
                        'is_visible' => true,
                        'meta' => ['label' => ['en' => 'Curated Selection', 'id' => 'Pilihan Terkurasi', 'es' => 'Selección curada']],
                        'title' => [
                            'en' => 'Our Destinations',
                            'id' => 'Destinasi Kami',
                            'es' => 'Nuestros Destinos',
                        ],
                        'content' => [
                            'en' => 'Discover handpicked journeys crafted for the world\'s most discerning travelers.',
                            'id' => 'Temukan perjalanan pilihan yang dirancang untuk para pelancong paling istimewa di dunia.',
                            'es' => 'Descubra viajes seleccionados diseñados para los viajeros más exigentes del mundo.',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($pages as $data) {
            $page = Page::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'image_path' => $data['image_path'],
                    'title' => $data['title'],
                ]
            );

            // Sync sections
            foreach ($data['sections'] as $sectionData) {
                $section = PageSection::updateOrCreate(
                    ['page_id' => $page->id, 'key' => $sectionData['key']],
                    [
                        'type' => $sectionData['type'],
                        'sort_order' => $sectionData['sort_order'],
                        'is_visible' => $sectionData['is_visible'],
                        'meta' => $sectionData['meta'],
                        'title' => $sectionData['title'],
                        'content' => $sectionData['content'] ?? null,
                    ]
                );

                if (isset($sectionData['features'])) {
                    $section->features()->delete();
                    foreach ($sectionData['features'] as $featureData) {
                        $section->features()->create($featureData);
                    }
                }
            }
        }
    }
}
