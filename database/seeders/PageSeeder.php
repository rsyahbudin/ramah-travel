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
                            'label' => [
                                'en' => 'Travel that Stays with You.',
                                'id' => 'Perjalanan yang Membekas.',
                                'es' => 'El Futuro de la Exploración',
                            ],
                            'cta_text' => [
                                'en' => 'Discover More',
                                'id' => 'Jelajahi Lebih Lanjut',
                                'es' => 'Descubrir más',
                            ],
                            'cta_secondary_text' => [
                                'en' => 'Our Story',
                                'id' => 'Cerita Kami',
                                'es' => 'Nuestra historia',
                            ],
                            'cta_link' => '/destinations',
                        ],
                        'title' => [
                            'en' => "Meet Indonesia.\nNot just see it.",
                            'id' => 'Temui Indonesia yang Sesungguhnya.',
                            'es' => "Redefiniendo el\nArte de Viajar.",
                        ],
                        'content' => [
                            'en' => 'Ocean, jungle, everyday life. And the people who actually live it. Trips built around local hosts and real communities, for travelers who want more than a highlight reel.',
                            'id' => 'Laut, hutan, kehidupan sehari-hari. Perjalanan yang dirancang bersama orang-orang lokal, untuk Anda yang ingin benar-benar mengenal Indonesia.',
                            'es' => 'Experimente los rincones más recónditos del mundo a través de una lente de lujo absoluto y exclusividad curada.',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'key' => 'home_about',
                        'sort_order' => 2,
                        'is_visible' => true,
                        'meta' => [
                            'label' => ['en' => 'Our Story', 'id' => 'Kisah Kami', 'es' => 'Desde 2008'],
                            'stat_number' => ['en' => '', 'id' => '', 'es' => '15+'],
                            'stat_text' => ['en' => '', 'id' => '', 'es' => 'Años creando experiencias a medida'],
                            'cta_text' => ['en' => 'Read Our Story', 'id' => 'Baca Cerita Kami', 'es' => 'Leer nuestra historia'],
                        ],
                        'title' => [
                            'en' => "The Story Behind \nRamah Indonesia.",
                            'id' => "Cerita di Balik\nRamah Indonesia",
                            'es' => "El viaje detrás de\nnuestro legado.",
                        ],
                        'content' => [
                            'en' => "Ramah means welcoming in Indonesian. It's also who we are.\n\nBetween us, we've traveled to all 38 provinces. Every region has its own culture, its own rhythm, its own way of doing things. And so many of them have everything it takes to be worth a trip. They just haven't been introduced properly yet.",
                            'id' => "Ada banyak daerah di Indonesia yang layak untuk dikenal, tapi belum pernah benar-benar diperkenalkan.\n\nKami membangun Ramah Indonesia karena percaya bahwa perjalanan yang baik bukan soal seberapa jauh Anda pergi. Tapi seberapa dalam Anda mengenalnya.",
                            'es' => 'Fundado en el principio de que los viajes deben ser tan únicos como el viajero. Curamos experiencias que trascienden lo ordinario.',
                        ],
                    ],
                    [
                        'type' => 'features',
                        'key' => 'home_experience_tiers',
                        'sort_order' => 3,
                        'is_visible' => true,
                        'meta' => ['label' => ['en' => 'Disesuaikan Untuk Anda', 'id' => 'Disesuaikan Untuk Anda', 'es' => 'A tu medida']],
                        'title' => [
                            'en' => 'Every Trip, Built Like This',
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
                                'icon' => 'map',
                                'sort_order' => 1,
                                'title' => [
                                    'en' => 'Beyond the Usual Itinerary',
                                    'id' => 'Destinasi yang Berbeda',
                                    'es' => 'Itinerarios a medida',
                                ],
                                'description' => [
                                    'en' => 'Themed journeys are built around local culture, communities, and experiences. Not just a list of places to tick off.',
                                    'id' => 'Perjalanan tematik ke daerah dan aktivitas yang jarang dilakukan.',
                                    'es' => 'Cada viaje se construye a medida desde cero.',
                                ],
                            ],
                            [
                                'icon' => 'group',
                                'sort_order' => 2,
                                'title' => [
                                    'en' => 'Led by Locals',
                                    'id' => 'Dipandu Oleh Lokal',
                                    'es' => 'Conserje de élite',
                                ],
                                'description' => [
                                    'en' => 'Every trip is handled by people who actually live there. They know the area, the community, and what makes each place worth the journey.',
                                    'id' => 'Setiap perjalanan ada di tangan orang yang benar-benar mengenal daerahnya, komunitasnya, dan apa yang membuat setiap tempat bernilai.',
                                    'es' => 'Soporte dedicado 24 horas al día, 7 días a la semana para cada capricho.',
                                ],
                            ],
                            [
                                'icon' => 'verified_user',
                                'sort_order' => 3,
                                'title' => [
                                    'en' => 'Travel Without the Hassle',
                                    'id' => 'Perjalanan Tanpa Repot',
                                    'es' => 'Acceso privilegiado',
                                ],
                                'description' => [
                                    'en' => 'From planning to the last day of your trip, everything is taken care of.',
                                    'id' => 'Dari perencanaan hingga hari terakhir, semua sudah kami tangani.',
                                    'es' => 'Obtenga entrada a fincas privadas y gemas ocultas.',
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
                            'en' => 'Ready to Go Deeper?',
                            'id' => 'Siap Melangkah Lebih Jauh?',
                            'es' => 'Mantente inspirado.',
                        ],
                        'content' => [
                            'en' => "Browse our destinations or get in touch. We'll help you figure out where to start.",
                            'id' => 'Jelajahi destinasi atau hubungi kami langsung. Kami siap membantu Anda menemukan perjalanan yang tepat.',
                            'es' => 'Únase a nuestro círculo íntimo para recibir actualizaciones exclusivas y conocimientos de viajes privados.',
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'about',
                'image_path' => null,
                'title' => [
                    'en' => 'The Story Behind Ramah Indonesia.',
                    'id' => 'Cerita di Balik Ramah Indonesia.',
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
                            'en' => 'The Story Behind Ramah Indonesia.',
                            'id' => 'Cerita di Balik Ramah Indonesia.',
                            'es' => 'El viaje detrás de nuestro legado.',
                        ],
                        'content' => [
                            'en' => 'The people, the places, and why we built this.',
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
                            'en' => "Ramah means welcoming in Indonesian. It's also who we are.\n\nWe started because we kept watching travelers arrive in Indonesia and leave, having only seen the surface of it. \n\nBeautiful places, yes. But the people, the conversations, the meals that don't have menus? Those were getting missed.\n\nBetween us, we've traveled to all 38 provinces. Every region has its own culture, its own rhythm, its own way of doing things. And so many of them have everything it takes to be worth a trip. They just haven't been introduced properly yet.\n\nSo we built Ramah. Not just to show people Indonesia, but to help them actually meet it.",
                            'id' => "Ada banyak daerah di Indonesia yang layak untuk dikenal, tapi belum pernah benar-benar diperkenalkan.\n\nBukan karena tidak menarik. Tapi kebanyakan perjalanan hanya menyentuh permukaannya saja. Tempat-tempat yang indah, ya. Tapi orang-orangnya, cara hidupnya, cerita di balik setiap daerah? Itu yang sering terlewat.\n\nKami membangun Ramah Indonesia karena percaya bahwa perjalanan yang baik bukan soal seberapa jauh Anda pergi. Tapi seberapa dalam Anda mengenalnya. Bersama komunitas lokal yang menjadi bagian dari setiap perjalanan kami, kami hadir untuk mempertemukan Anda dengan Indonesia yang selama ini belum sempat Anda temui.",
                            'es' => 'Fundada según el principio de que viajar debe ser tan único como el viajero, Ramah Indonesia ha estado curando viajes extraordinarios desde 2008. Creemos en el poder de la exploración para transformar vidas y nos dedicamos a descubrir los rincones más recónditos del mundo a través de una lente de lujo absoluto y exclusividad curada.',
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
                            'en' => 'Places worth the journey, introduced by the people who know them best.',
                            'id' => 'Tempat-tempat pilihan, dengan perjalanan yang dirancang bersama komunitas lokal.',
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
                    'image_path' => $data['image_path'] ?? null,
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
