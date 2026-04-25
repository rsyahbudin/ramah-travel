<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Language::all(); // Ensure languages are present

        /**
         * Format:
         *   'key' => 'plain value'                       → type: text
         *   'key' => ['en' => '...', 'id' => '...']     → type: translatable
         *   'key' => [['icon'=>..., 'title'=>...], ...]  → type: json (structured, non-translatable)
         */
        $settings = [
            // Identity
            'site_name' => ['type' => 'text', 'value' => 'Ramah Indonesia'],
            'logo_image' => ['type' => 'text', 'value' => 'settings/TJ8tF2SEDgcdOnXa1Jiwh0OahaMucgKK95AQoLy3.png'],
            'logo_white' => ['type' => 'text', 'value' => 'settings/eZxuAH2ywebZSHS4E1faWUOL0QfGiljlkWOtmN9Q.png'],

            // Contact & Social
            'whatsapp_number' => ['type' => 'text', 'value' => '628123456789'],
            'admin_email' => ['type' => 'text', 'value' => 'admin@ramahindonesia.com'],
            'social_instagram' => ['type' => 'text', 'value' => 'https://instagram.com/ramah__indonesia'],
            'social_facebook' => ['type' => 'text', 'value' => ''],
            'social_twitter' => ['type' => 'text', 'value' => ''],
            'social_youtube' => ['type' => 'text', 'value' => ''],
            'social_tiktok' => ['type' => 'text', 'value' => ''],

            // Translatable settings
            'footer_text' => [
                'type' => 'translatable',
                'value' => [
                    'en' => 'Indonesia-based. Community-driven. Built for curious travelers.',
                    'id' => 'Menciptakan perjalanan luar biasa bagi para pelancong paling cerdas di dunia.',
                    'es' => 'Creando viajes extraordinarios para los viajeros más exigentes del mundo.',
                ],
            ],
            'whatsapp_template' => [
                'type' => 'translatable',
                'value' => [
                    'en' => 'Hello, my name is {name}. I would like to book {destination} for {person} pax on {travel_date}. I am from {city}, {country}. Email: {email}, Phone: {phone}.',
                    'id' => 'Halo, nama saya {name}. Saya ingin memesan {destination} untuk {person} orang pada tanggal {travel_date}. Saya dari {city}, {country}. Email: {email}, No. HP: {phone}.',
                    'es' => 'Hola, mi nombre es {name}. Me gustaría reservar {destination} para {person} personas el {travel_date}. Soy de {city}, {country}. Email: {email}, Teléfono: {phone}.',
                ],
            ],
            'email_subject_template' => [
                'type' => 'translatable',
                'value' => [
                    'en' => 'New Booking Inquiry: {destination} - {name}',
                    'id' => 'Pertanyaan Pesanan Baru: {destination} - {name}',
                    'es' => 'Nueva consulta de reserva: {destination} - {name}',
                ],
            ],
            'email_template' => [
                'type' => 'translatable',
                'value' => [
                    'en' => "New Inquiry from {name} ({email}).\n\nDestination: {destination}\nTravel Date: {travel_date}\nPax: {person}\nPhone: {phone}\nCity/Country: {city}, {country}\n\nURL: {url}",
                    'id' => "Pertanyaan Baru dari {name} ({email}).\n\nDestinasi: {destination}\nTanggal: {travel_date}\nOrang: {person}\nNo. HP: {phone}\nKota/Negara: {city}, {country}\n\nURL: {url}",
                    'es' => "Nueva consulta de {name} ({email}).\n\nDestino: {destination}\nFecha: {travel_date}\nPersonas: {person}\nTeléfono: {phone}\nCiudad/País: {city}, {country}\n\nURL: {url}",
                ],
            ],
            'whatsapp_general_template' => [
                'type' => 'translatable',
                'value' => [
                    'en' => 'Hello, saya tertarik untuk melakukan trip. Apakah bisa diberikan detailnya?',
                    'id' => 'Hello, saya tertarik untuk melakukan trip. Apakah bisa diberikan detailnya?',
                    'es' => 'Hello, saya tertarik untuk melakukan trip. Apakah bisa diberikan detailnya?',
                ],
            ],
        ];

        foreach ($settings as $key => $config) {
            $isTranslatable = ($config['type'] ?? '') === 'translatable';

            $setting = Setting::updateOrCreate(
                ['key' => $key],
                [
                    'type' => $config['type'],
                    'value' => $isTranslatable ? null : ($config['value'] ?? null),
                ]
            );

            if ($isTranslatable && is_array($config['value'])) {
                $setting->syncTranslations($config['value']);
            }
        }
    }
}
