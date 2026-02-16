<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => [
                    'en' => 'The Journey Behind Our Legacy.',
                    'id' => 'Perjalanan di Balik Warisan Kami.',
                    'es' => 'El viaje detrás de nuestro legado.',
                ],
                'slug' => 'about',
                'content' => [
                    'en' => "Founded on the principle that travel should be as unique as the traveler, Ramah Travel has been curating extraordinary journeys since 2008. We believe in the power of exploration to transform lives, and we dedicate ourselves to uncovering the world's most secluded corners through a lens of absolute luxury and curated exclusivity.",
                    'id' => 'Didirikan berdasarkan prinsip bahwa perjalanan harus seunik pelancongnya, Ramah Travel telah mengkurasi perjalanan luar biasa sejak 2008. Kami percaya pada kekuatan eksplorasi untuk mengubah hidup, dan kami mendedikasikan diri untuk mengungkap sudut-sudut paling terpencil di dunia melalui lensa kemewahan mutlak dan eksklusivitas yang terkurasi.',
                    'es' => 'Fundada según el principio de que viajar debe ser tan único como el viajero, Ramah Travel ha estado curando viajes extraordinarios desde 2008. Creemos en el poder de la exploración para transformar vidas y nos dedicamos a descubrir los rincones más recónditos del mundo a través de una lente de lujo absoluto y exclusividad curada.',
                ],
            ],
        ];

        foreach ($pages as $data) {
            $data['title'] = json_encode($data['title']);
            $data['content'] = json_encode($data['content']);
            Page::create($data);
        }
    }
}
