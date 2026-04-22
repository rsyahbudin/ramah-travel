<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $languages = Language::all()->keyBy('code');

        /**
         * Format:
         *   'key' => 'plain value'                       → type: text
         *   'key' => ['en' => '...', 'id' => '...']     → type: translatable
         *   'key' => [['icon'=>..., 'title'=>...], ...]  → type: json (structured, non-translatable)
         */
        $settings = [
            // Plain settings (non-translatable)
            'site_name' => ['type' => 'text', 'value' => 'Ramah Indonesia'],
            'whatsapp_number' => ['type' => 'text', 'value' => '628123456789'],
            'admin_email' => ['type' => 'text', 'value' => 'admin@ramah-travel.com'],
            'social_instagram' => ['type' => 'text', 'value' => 'https://instagram.com/ramahtravel'],
            'social_facebook' => ['type' => 'text', 'value' => 'https://facebook.com/ramahtravel'],

            // Translatable settings
            'footer_text' => [
                'type' => 'translatable',
                'translations' => [
                    'en' => 'Crafting extraordinary journeys for the world\'s most discerning travelers.',
                    'id' => 'Menciptakan perjalanan luar biasa bagi para pelancong paling cerdas di dunia.',
                    'es' => 'Creando viajes extraordinarios para los viajeros más exigentes del mundo.',
                ],
            ],
            'whatsapp_template' => [
                'type' => 'translatable',
                'translations' => [
                    'en' => 'Hello, my name is {name}. I would like to book {destination} for {person} pax on {travel_date}. I am from {city}, {country}. Email: {email}, Phone: {phone}.',
                    'id' => 'Halo, nama saya {name}. Saya ingin memesan {destination} untuk {person} orang pada tanggal {travel_date}. Saya dari {city}, {country}. Email: {email}, No. HP: {phone}.',
                    'es' => 'Hola, mi nombre es {name}. Me gustaría reservar {destination} para {person} personas el {travel_date}. Soy de {city}, {country}. Email: {email}, Teléfono: {phone}.',
                ],
            ],
            'email_subject_template' => [
                'type' => 'translatable',
                'translations' => [
                    'en' => 'New Booking Inquiry: {destination} - {name}',
                    'id' => 'Pertanyaan Pesanan Baru: {destination} - {name}',
                    'es' => 'Nueva consulta de reserva: {destination} - {name}',
                ],
            ],
            'email_template' => [
                'type' => 'translatable',
                'translations' => [
                    'en' => "New Inquiry from {name} ({email}).\n\nDestination: {destination}\nTravel Date: {travel_date}\nPax: {person}\nPhone: {phone}\nCity/Country: {city}, {country}\n\nURL: {url}",
                    'id' => "Pertanyaan Baru dari {name} ({email}).\n\nDestinasi: {destination}\nTanggal: {travel_date}\nOrang: {person}\nNo. HP: {phone}\nKota/Negara: {city}, {country}\n\nURL: {url}",
                    'es' => "Nueva consulta de {name} ({email}).\n\nDestino: {destination}\nFecha: {travel_date}\nPersonas: {person}\nTeléfono: {phone}\nCiudad/País: {city}, {country}\n\nURL: {url}",
                ],
            ],
            'whatsapp_general_template' => [
                'type' => 'translatable',
                'translations' => [
                    'en' => 'Hello, I am interested in booking a trip. Could you provide more information?',
                    'id' => 'Halo, saya tertarik untuk memesan perjalanan. Bisakah Anda memberikan informasi lebih lanjut?',
                    'es' => 'Hola, estoy interesado en reservar un viaje. ¿Podría darme más información?',
                ],
            ],
        ];

        foreach ($settings as $key => $config) {
            $setting = Setting::updateOrCreate(
                ['key' => $key],
                [
                    'type' => $config['type'],
                    'value' => $config['value'] ?? null,
                ]
            );

            if ($config['type'] === 'translatable') {
                foreach ($config['translations'] as $code => $value) {
                    if (! isset($languages[$code])) {
                        continue;
                    }

                    $setting->translationsRelation()->updateOrCreate(
                        ['language_id' => $languages[$code]->id],
                        ['value' => $value]
                    );
                }
            }
        }
    }
}
