<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'Ramah Travel',
            'footer_text' => [
                'en' => 'Crafting extraordinary journeys for the world\'s most discerning travelers.',
                'id' => 'Menciptakan perjalanan luar biasa bagi para pelancong paling cerdas di dunia.',
                'es' => 'Creando viajes extraordinarios para los viajeros más exigentes del mundo.',
            ],
            'whatsapp_number' => '628123456789',
            'admin_email' => 'admin@ramah-travel.com',
            'social_instagram' => 'https://instagram.com/ramahtravel',
            'social_facebook' => 'https://facebook.com/ramahtravel',

            // Hero
            'hero_title' => [
                'en' => "Redefining the\nArt of Travel.",
                'id' => "Mendefinisikan Ulang\nSeni Perjalanan.",
                'es' => "Redefiniendo el\nArte de Viajar.",
            ],
            'hero_subtitle' => [
                'en' => 'Experience the world\'s most secluded corners through a lens of absolute luxury and curated exclusivity.',
                'id' => 'Nikmati sudut-sudut paling terpencil di dunia melalui lensa kemewahan mutlak dan eksklusivitas yang terkurasi.',
                'es' => 'Experimente los rincones más recónditos del mundo a través de una lente de lujo absoluto y exclusividad curada.',
            ],
            'hero_cta_text' => [
                'en' => 'Discover More',
                'id' => 'Jelajahi Lebih Lanjut',
                'es' => 'Descubrir más',
            ],
            'hero_label' => [
                'en' => 'The Future of Exploration',
                'id' => 'Masa Depan Eksplorasi',
                'es' => 'El Futuro de la Exploración',
            ],

            // About
            'about_title' => [
                'en' => "The Journey Behind\nOur Legacy.",
                'id' => "Perjalanan di Balik\nWarisan Kami.",
                'es' => "El viaje detrás de\nnuestro legado.",
            ],
            'about_content' => [
                'en' => 'Founded on the principle that travel should be as unique as the traveler. We curate experiences that transcend the ordinary.',
                'id' => 'Didirikan berdasarkan prinsip bahwa perjalanan harus seunik pelancongnya. Kami mengkurasi pengalaman yang melampaui kebiasaan.',
                'es' => 'Fundado en el principio de que los viajes deben ser tan únicos como el viajero. Curamos experiencias que trascienden lo ordinario.',
            ],
            'about_label' => [
                'en' => 'Since 2008',
                'id' => 'Sejak 2008',
                'es' => 'Desde 2008',
            ],
            'about_stat_number' => [
                'en' => '15+',
                'id' => '15+',
                'es' => '15+',
            ],
            'about_stat_text' => [
                'en' => 'Years of Crafting Bespoke Experiences',
                'id' => 'Tahun Menciptakan Pengalaman Kustom',
                'es' => 'Años creando experiencias a medida',
            ],

            // Experience Tiers
            'experience_tiers_title' => [
                'en' => 'How We Travel',
                'id' => 'Cara Kami Bepergian',
                'es' => 'Cómo viajamos',
            ],
            'experience_tiers_label' => [
                'en' => 'Tailored For You',
                'id' => 'Disesuaikan Untuk Anda',
                'es' => 'A tu medida',
            ],
            'experience_tiers_points' => [
                'en' => [
                    ['icon' => 'diamond', 'title' => 'Elite Concierge', 'description' => '24/7 dedicated support for every whim.'],
                    ['icon' => 'map', 'title' => 'Bespoke Itineraries', 'description' => 'Every journey is custom-built from the ground up.'],
                    ['icon' => 'verified_user', 'title' => 'Insider Access', 'description' => 'Gain entry to private estates and hidden gems.'],
                ],
                'id' => [
                    ['icon' => 'diamond', 'title' => 'Layanan Pramutamu Elit', 'description' => 'Dukungan khusus 24/7 untuk setiap keinginan Anda.'],
                    ['icon' => 'map', 'title' => 'Rencana Perjalanan Kustom', 'description' => 'Setiap perjalanan dibuat khusus dari awal.'],
                    ['icon' => 'verified_user', 'title' => 'Akses Eksklusif', 'description' => 'Dapatkan akses ke properti pribadi dan permata tersembunyi.'],
                ],
                'es' => [
                    ['icon' => 'diamond', 'title' => 'Conserje de élite', 'description' => 'Soporte dedicado 24 horas al día, 7 días a la semana para cada capricho.'],
                    ['icon' => 'map', 'title' => 'Itinerarios a medida', 'description' => 'Cada viaje se construye a medida desde cero.'],
                    ['icon' => 'verified_user', 'title' => 'Acceso privilegiado', 'description' => 'Obtenga entrada a fincas privadas y gemas ocultas.'],
                ],
            ],

            // CTA
            'cta_title' => [
                'en' => 'Stay Inspired.',
                'id' => 'Tetap Terinspirasi.',
                'es' => 'Mantente inspirado.',
            ],
            'cta_subtitle' => [
                'en' => 'Join our inner circle for exclusive updates and private travel insights.',
                'id' => 'Bergabunglah dengan lingkaran dalam kami untuk pembaruan eksklusif dan wawasan perjalanan pribadi.',
                'es' => 'Únase a nuestro círculo íntimo para recibir actualizaciones exclusivas y conocimientos de viajes privados.',
            ],

            // About Page Specifics
            'about_hero_label' => [
                'en' => 'Our Story',
                'id' => 'Cerita Kami',
                'es' => 'Nuestra historia',
            ],
            'about_hero_subtitle' => [
                'en' => 'The journey behind our legacy and the passion that drives us.',
                'id' => 'Perjalanan di balik warisan kami dan semangat yang menggerakkan kami.',
                'es' => 'El viaje tras nuestro legado y la pasión que nos impulsa.',
            ],
            'about_who_we_are_label' => [
                'en' => 'Who We Are',
                'id' => 'Siapa Kami',
                'es' => 'Quiénes somos',
            ],

            // Templates
            'whatsapp_template' => [
                'en' => 'Hello, my name is {name}. I would like to book {destination} for {person} pax on {date}. I am from {city}, {country}. Email: {email}, Phone: {phone}.',
                'id' => 'Halo, nama saya {name}. Saya ingin memesan {destination} untuk {person} orang pada tanggal {date}. Saya dari {city}, {country}. Email: {email}, No. HP: {phone}.',
                'es' => 'Hola, mi nombre es {name}. Me gustaría reservar {destination} para {person} personas el {date}. Soy de {city}, {country}. Email: {email}, Teléfono: {phone}.',
            ],
            'email_subject_template' => [
                'en' => 'New Booking Inquiry: {destination} - {name}',
                'id' => 'Pertanyaan Pesanan Baru: {destination} - {name}',
                'es' => 'Nueva consulta de reserva: {destination} - {name}',
            ],
            'email_template' => [
                'en' => "New Inquiry from {name} ({email}).\n\nDestination: {destination}\nDate: {date}\nPax: {person}\nPhone: {phone}\nCity/Country: {city}, {country}\n\nURL: {url}",
                'id' => "Pertanyaan Baru dari {name} ({email}).\n\nDestinasi: {destination}\nTanggal: {date}\nOrang: {person}\nNo. HP: {phone}\nKota/Negara: {city}, {country}\n\nURL: {url}",
                'es' => "Nueva consulta de {name} ({email}).\n\nDestino: {destination}\nFecha: {date}\nPersonas: {person}\nTeléfono: {phone}\nCiudad/País: {city}, {country}\n\nURL: {url}",
            ],
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : $value]
            );
        }
    }
}
