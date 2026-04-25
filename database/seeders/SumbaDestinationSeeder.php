<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Seeder;

class SumbaDestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destination = Destination::updateOrCreate(
            ['slug' => 'east-west-sumba'],
            [
            'price' => 1040,
            'price_max' => 1093,
            'is_featured' => true,
            'is_visible' => true,
            'title' => [
                'en' => 'East & West Sumba Journey',
                'id' => 'Perjalanan Sumba Timur & Barat',
                'es' => 'Viaje Sumba Este y Oeste',
            ],
            'location' => [
                'en' => 'East & West Sumba, East Nusa Tenggara, Indonesia',
                'id' => 'Sumba Timur & Barat, Nusa Tenggara Timur, Indonesia',
                'es' => 'Sumba Este y Oeste, Nusa Tenggara Timur, Indonesia',
            ],
            'duration' => [
                'en' => '10 Days 9 Nights',
                'id' => '10 Hari 9 Malam',
                'es' => '10 Días 9 Noches',
            ],
            'theme' => [
                'en' => 'Nature, Culture, Coastal',
                'id' => 'Alam, Budaya, Pesisir',
                'es' => 'Naturaleza, Cultura, Costa',
            ],
            'description' => [
                'en' => 'A 10-day journey across East and West Sumba, moving from coastal areas in the west to savanna landscapes in the east. Along the way, you’ll pass through lagoons, waterfalls, and traditional villages, while also seeing the process of traditional weaving, with a pace that allows you to experience each place without rushing.',
                'id' => 'Perjalanan 10 hari menjelajahi Sumba Timur dan Barat, bergerak dari wilayah pesisir di bagian barat hingga lanskap savana di timur. Sepanjang perjalanan, kamu akan melewati laguna, air terjun, desa tradisional, serta melihat langsung proses pembuatan kain tenun dengan ritme yang cukup tenang untuk menikmati setiap tempat tanpa terburu-buru.',
                'es' => 'Un viaje de 10 días por Sumba Este y Oeste, desde las zonas costeras del oeste hasta paisajes de sabana en el este. A lo largo del recorrido, pasarás por lagunas, cascadas y pueblos tradicionales, y podrás ver el proceso de tejido tradicional, con un ritmo cómodo para disfrutar cada lugar sin prisas.',
            ],
            'highlights' => [
                'en' => "• Weekuri Lagoon\n• Ratenggaro Village\n• Mandorak Beach\n• Tanggedu Waterfall\n• Waimarang Waterfall\n• Wairinding Hills\n• Tenau Hills\n• Piarakuku Hills\n• Walakiri Beach (Dancing Mangroves)\n• Ikat Weaving Experience",
                'id' => "• Weekuri Lagoon\n• Desa Ratenggaro\n• Pantai Mandorak\n• Air Terjun Tanggedu\n• Air Terjun Waimarang\n• Bukit Wairinding\n• Bukit Tenau\n• Bukit Piarakuku\n• Pantai Walakiri (Dancing Mangroves)\n• Pengalaman Tenun Ikat",
                'es' => "• Weekuri Lagoon\n• Pueblo de Ratenggaro\n• Playa Mandorak\n• Cascada Tanggedu\n• Cascada Waimarang\n• Colinas de Wairinding\n• Colinas de Tenau\n• Colinas de Piarakuku\n• Playa Walakiri (manglares danzantes)\n• Experiencia de tejido ikat",
            ],
        ]);

        // Cleanup existing relations to prevent duplicates on re-run
        $destination->tripInfos()->delete();
        $destination->includeItems()->delete();
        $destination->excludeItems()->delete();
        $destination->faqs()->delete();
        $destination->itineraryItems()->delete();

        // Trip Info
        $infos = [
            [
                'label' => ['en' => 'Duration', 'id' => 'Durasi', 'es' => 'Duración'],
                'value' => ['en' => '10 Days 9 Nights', 'id' => '10 Hari 9 Malam', 'es' => '10 Días 9 Noches'],
            ],
            [
                'label' => ['en' => 'Type', 'id' => 'Tipe', 'es' => 'Tipo'],
                'value' => ['en' => 'Private', 'id' => 'Private', 'es' => 'Privado'],
            ],
            [
                'label' => ['en' => 'Group', 'id' => 'Grup', 'es' => 'Grupo'],
                'value' => ['en' => 'Max 4 pax', 'id' => 'Maks 4 orang', 'es' => 'Máx. 4 personas'],
            ],
            [
                'label' => ['en' => 'Stay', 'id' => 'Akomodasi', 'es' => 'Alojamiento'],
                'value' => ['en' => '4★ Hotels / Resorts', 'id' => 'Hotel / Resort 4★', 'es' => 'Hoteles / Resorts 4★'],
            ],
            [
                'label' => ['en' => 'Focus', 'id' => 'Fokus', 'es' => 'Enfoque'],
                'value' => ['en' => 'Nature & Culture', 'id' => 'Alam & Budaya', 'es' => 'Naturaleza y cultura'],
            ],
            [
                'label' => ['en' => 'Region', 'id' => 'Wilayah', 'es' => 'Región'],
                'value' => ['en' => 'East & West Sumba', 'id' => 'Sumba Timur & Barat', 'es' => 'Sumba Este y Oeste'],
            ],
        ];

        foreach ($infos as $index => $info) {
            $destination->tripInfos()->create([
                'sort_order' => $index,
                'label' => $info['label'],
                'value' => $info['value'],
            ]);
        }

        // Includes
        $includes = [
            ['en' => 'Accommodation in 4-star hotels/resorts (room with extra bed or 2-bedroom villa)', 'id' => 'Akomodasi di hotel/resort bintang 4 (kamar dengan extra bed atau villa 2 kamar)', 'es' => 'Alojamiento en hoteles/resorts de 4 estrellas (habitación con cama extra o villa de 2 dormitorios)'],
            ['en' => 'Private car, fuel, and parking throughout the trip', 'id' => 'Mobil pribadi, bahan bakar, dan parkir selama perjalanan', 'es' => 'Transporte privado con combustible y estacionamiento durante el viaje'],
            ['en' => 'English-speaking driver-guide', 'id' => 'Driver-guide berbahasa Inggris', 'es' => 'Conductor-guía de habla inglesa'],
            ['en' => 'Entrance fees to all destinations', 'id' => 'Tiket masuk ke seluruh destinasi', 'es' => 'Entradas a todos los destinos'],
            ['en' => 'National park tickets', 'id' => 'Tiket taman nasional', 'es' => 'Entradas al parque nacional'],
            ['en' => 'Mineral water during the trip', 'id' => 'Air mineral selama perjalanan', 'es' => 'Agua mineral durante el viaje'],
            ['en' => 'Tax and service charge', 'id' => 'Pajak dan biaya layanan', 'es' => 'Impuestos y cargos por servicio'],
        ];

        foreach ($includes as $index => $label) {
            $destination->includeItems()->create([
                'type' => 'include',
                'sort_order' => $index,
                'label' => $label,
            ]);
        }

        // Excludes
        $excludes = [
            ['en' => 'Flights (available as add-on, arranged by Ramah Indonesia)', 'id' => 'Tiket pesawat (tersedia sebagai add-on, diatur oleh Ramah Indonesia)', 'es' => 'Vuelos (disponibles como add-on, organizados por Ramah Indonesia)'],
            ['en' => 'Lunch and dinner (breakfast is provided daily at the hotel)', 'id' => 'Makan siang dan makan malam (sarapan tersedia setiap hari di hotel)', 'es' => 'Almuerzo y cena (el desayuno está incluido diariamente en el hotel)'],
            ['en' => 'Personal expenses', 'id' => 'Pengeluaran pribadi', 'es' => 'Gastos personales'],
        ];

        foreach ($excludes as $index => $label) {
            $destination->excludeItems()->create([
                'type' => 'exclude',
                'sort_order' => $index,
                'label' => $label,
            ]);
        }

        // FAQs
        $faqs = [
            [
                'question' => ['en' => 'Is this trip suitable for beginners?', 'id' => 'Apakah perjalanan ini cocok untuk pemula?', 'es' => '¿Este viaje es adecuado para principiantes?'],
                'answer' => ['en' => 'Yes, the itinerary is designed with a comfortable pace and is suitable for most travelers. Some locations may require short walks.', 'id' => 'Ya, itinerary disusun dengan ritme yang nyaman dan cocok untuk sebagian besar traveler. Beberapa lokasi hanya memerlukan jalan kaki singkat.', 'es' => 'Sí, el itinerario está diseñado con un ritmo cómodo y es apto para la mayoría de los viajeros. Algunas ubicaciones pueden requerir caminatas cortas.'],
            ],
            [
                'question' => ['en' => 'What is the group size?', 'id' => 'Berapa jumlah peserta dalam satu perjalanan?', 'es' => '¿Cuál es el tamaño del grupo?'],
                'answer' => ['en' => 'The trip is private, with a maximum of 4 people.', 'id' => 'Perjalanan ini bersifat private dengan maksimal 4 orang.', 'es' => 'El viaje es privado, con un máximo de 4 personas.'],
            ],
            [
                'question' => ['en' => 'Are flights included in the package?', 'id' => 'Apakah tiket pesawat sudah termasuk?', 'es' => '¿Los vuelos están incluidos?'],
                'answer' => ['en' => 'Flights are not included, but can be arranged as an add-on.', 'id' => 'Tiket pesawat tidak termasuk, namun dapat diatur sebagai add-on.', 'es' => 'Los vuelos no están incluidos, pero pueden organizarse como un add-on.'],
            ],
            [
                'question' => ['en' => 'What meals are included?', 'id' => 'Makanan apa saja yang termasuk?', 'es' => '¿Qué comidas están incluidas?'],
                'answer' => ['en' => 'Daily breakfast is included. Lunch and dinner are not included, but can be arranged during the trip.', 'id' => 'Sarapan harian sudah termasuk. Makan siang dan makan malam tidak termasuk, namun dapat diatur selama perjalanan.', 'es' => 'El desayuno diario está incluido. El almuerzo y la cena no están incluidos, pero pueden organizarse durante el viaje.'],
            ],
            [
                'question' => ['en' => 'What should I prepare for the trip?', 'id' => 'Apa yang perlu dipersiapkan?', 'es' => '¿Qué debo preparar para el viaje?'],
                'answer' => ['en' => 'Comfortable clothing, walking shoes, sun protection, and personal essentials are recommended.', 'id' => 'Disarankan membawa pakaian nyaman, sepatu untuk berjalan, perlindungan dari matahari, serta kebutuhan pribadi.', 'es' => 'Se recomienda llevar ropa cómoda, calzado para caminar, protección solar y artículos personales.'],
            ],
            [
                'question' => ['en' => 'Can the itinerary be customized?', 'id' => 'Apakah itinerary bisa disesuaikan?', 'es' => '¿Se puede personalizar el itinerario?'],
                'answer' => ['en' => 'Yes, the itinerary is designed to be comfortable and flexible. Timings can be adjusted on the day based on your pace, energy, and interests.', 'id' => 'Ya, itinerary disusun dengan ritme yang nyaman dan fleksibel. Waktu kunjungan dapat disesuaikan selama perjalanan berdasarkan pace, kondisi, dan minat', 'es' => 'Sí, el itinerario está diseñado para ser cómodo y flexible. Los horarios pueden ajustarse durante el viaje según su ritmo, energía e intereses.'],
            ],
        ];

        foreach ($faqs as $index => $data) {
            $destination->faqs()->create([
                'sort_order' => $index,
                'question' => $data['question'],
                'answer' => $data['answer'],
            ]);
        }

        // Detailed 10 Days Itinerary
        $itinerary = [
            [
                'day' => 1,
                'title' => ['en' => 'Arrival at Tambolaka & Coastal Gems', 'id' => 'Kedatangan di Tambolaka & Permata Pesisir', 'es' => 'Llegada a Tambolaka y Joyas Costeras'],
                'description' => [
                    'en' => 'Arrive at Tambolaka Airport and meet your private driver-guide. Begin the journey with lunch at a local spot before visiting New Cave & Lagoon, a coastal cave and hidden lagoon in West Sumba. Spend the afternoon at Weekuri Lagoon, known for its calm turquoise water, then check in at ARYA Sumba and enjoy free time.',
                    'id' => 'Tiba di Bandara Tambolaka dan bertemu driver-guide. Memulai perjalanan dengan makan siang di tempat lokal. Setelah itu mengunjungi New Cave & Lagoon, area gua dan laguna di pesisir Sumba Barat, sebelum menghabiskan sore di Weekuri Lagoon. Dilanjutkan dengan check-in di ARYA Sumba dan waktu bebas untuk beristirahat.',
                    'es' => 'Llegada al aeropuerto de Tambolaka y encuentro con el guía, seguido de un almuerzo en un lugar local. Después, visita a New Cave & Lagoon, una cueva y laguna costera en Sumba Oeste, antes de pasar la tarde en Weekuri Lagoon. Check-in en ARYA Sumba y tiempo libre para descansar.',
                ],
            ],
            [
                'day' => 2,
                'title' => ['en' => 'Traditional Villages & Secluded Coves', 'id' => 'Desa Tradisional & Teluk Tersembunyi', 'es' => 'Pueblos Tradicionales y Calas Escondidas'],
                'description' => [
                    'en' => 'Start the day from ARYA Sumba and visit Ratenggaro Village, known for its traditional Uma Mbatangu houses and megalithic tombs along the southwestern coast. Continue to Weepeneba Lagoon, a quieter coastal spot, then have lunch at ARYA or Cap Karoso. In the afternoon, visit Mandorak Beach, a secluded cove surrounded by cliffs, then return to the hotel to rest.',
                    'id' => 'Memulai hari dari ARYA Sumba, dilanjutkan dengan mengunjungi Desa Ratenggaro yang dikenal dengan rumah adat Uma Mbatangu dan kubur batu di pesisir barat daya. Perjalanan berlanjut ke Weepeneba Lagoon yang lebih tenang, lalu makan siang di ARYA atau Cap Karoso. Sore hari mengunjungi Pantai Mandorak, teluk kecil yang dikelilingi tebing, kemudian kembali ke hotel untuk beristirahat.',
                    'es' => 'Comienza el día desde ARYA Sumba con un recorrido por Ratenggaro, conocido por sus casas tradicionales Uma Mbatangu y tumbas megalíticas en la costa suroeste. Continúa hacia Weepeneba Lagoon, un lugar más tranquilo, luego almuerzo en ARYA o Cap Karoso. Por la tarde, pasarás por Mandorak Beach, una cala rodeada de acantilados, antes de regresar al hotel para descansar.',
                ],
            ],
            [
                'day' => 3,
                'title' => ['en' => 'Rice Fields & Forest Waterfalls', 'id' => 'Persawahan & Air Terjun Hutan', 'es' => 'Arrozales y Cascadas del Bosque'],
                'description' => [
                    'en' => 'Check out from ARYA Sumba and begin the journey toward the southern part of West Sumba. Start the day at Weekacura Rice Field, a stretch of terraced paddies with water flowing through the fields. Lunch follows at Foody Resto before continuing to Lokomboro Waterfall, a hidden waterfall in the forest reached by a short trek. In the afternoon, check in at Alamayah Resort and enjoy free time.',
                    'id' => 'Check-out dari ARYA Sumba dan memulai perjalanan menuju bagian selatan Sumba Barat. Perjalanan diawali dengan mengunjungi Weekacura Rice Field, area persawahan bertingkat dengan aliran air yang mengalir di tengah sawah. Setelah itu, makan siang di Foody Resto sebelum melanjutkan ke Air Terjun Lokomboro, air terjun tersembunyi di dalam hutan yang dapat dicapai dengan trekking singkat. Sore hari check-in di Alamayah Resort dan menikmati waktu bebas untuk beristirahat.',
                    'es' => 'Check-out de ARYA Sumba y comienzo del recorrido hacia el sur de Sumba Oeste. El día inicia en Weekacura Rice Field, un paisaje de arrozales en terrazas con agua que fluye entre los campos. Luego almuerzo en Foody Resto antes de continuar hacia Lokomboro Waterfall, una cascada escondida en el bosque accesible con una caminata corta. Por la tarde, check-in en Alamayah y tiempo libre.',
                ],
            ],
            [
                'day' => 4,
                'title' => ['en' => 'Coastal Life & Sunset at Watu Bela', 'id' => 'Kehidupan Pesisir & Matahari Terbenam di Watu Bela', 'es' => 'Vida Costera y Atardecer en Watu Bela'],
                'description' => [
                    'en' => 'Spend the day around Kerewei Beach, known as one of Sumba’s surf spots with open coastal views. For non-surfers, the area is also ideal for a relaxed walk along the shoreline. Lunch can be enjoyed at Alamayah or nearby spots with ocean views. In the afternoon, head to Watu Bela Beach for sunset before returning to Alamayah.',
                    'id' => 'Menghabiskan hari di sekitar Pantai Kerewei yang dikenal sebagai salah satu spot surf di Sumba dengan pemandangan pesisir yang terbuka. Bagi yang tidak berselancar, area ini juga cocok untuk berjalan santai menikmati garis pantai. Makan siang dapat dilakukan di Alamayah atau restoran sekitar dengan pemandangan laut. Sore hari dilanjutkan dengan menikmati matahari terbenam di Pantai Watu Bela sebelum kembali ke Alamayah.',
                    'es' => 'El día se pasa en la zona de Kerewei Beach, uno de los puntos de surf de Sumba con amplias vistas costeras. Para quienes no practican surf, también es un buen lugar para caminar y disfrutar del paisaje. Almuerzo en Alamayah o en restaurantes cercanos con vista al mar. Por la tarde, traslado a Watu Bela Beach para ver el atardecer antes de regresar a Alamayah.',
                ],
            ],
            [
                'day' => 5,
                'title' => ['en' => 'Weaving Workshop & Secluded Beaches', 'id' => 'Workshop Tenun & Pantai Tersembunyi', 'es' => 'Taller de Tejido y Playas Escondidas'],
                'description' => [
                    'en' => 'Start the day with a visit to Karaja Tenun Workshop to observe the traditional weaving process. Continue to Dasang Beach, a quieter stretch of coastline ideal for a relaxed walk or a swim. Lunch can be enjoyed at Alamayah or nearby. In the afternoon, head to Kerewei Beach for surfing or to enjoy the sunset.',
                    'id' => 'Memulai hari dengan mengunjungi Karaja Tenun Workshop untuk melihat proses pembuatan kain tenun secara langsung. Dilanjutkan ke Pantai Dasang, area pesisir yang lebih sepi dan cocok untuk berjalan santai atau menikmati suasana. Makan siang dapat dilakukan di Alamayah atau restoran sekitar. Sore hari kembali ke Pantai Kerewei untuk berselancar atau menikmati matahari terbenam.',
                    'es' => 'El día comienza con una visita a Karaja Tenun Workshop para observar el proceso tradicional de tejido. Continúa hacia Dasang Beach, una zona costera más tranquila ideal para caminar o nadar. Almuerzo en Alamayah o en restaurantes cercanos. Por la tarde, traslado a Kerewei Beach para surf o para disfrutar del atardecer.',
                ],
            ],
            [
                'day' => 6,
                'title' => ['en' => 'Crossing to East Sumba & Savanna Sunsets', 'id' => 'Menyeberang ke Sumba Timur & Savana', 'es' => 'Cruce a Sumba Este y Atardeceres de Sabana'],
                'description' => [
                    'en' => 'Check out from Alamayah and begin the journey toward East Sumba. Along the way, the first stop is Lapopu Waterfall, located within Manupeu Tanah Daru National Park, reached by a short walk. Continue with lunch at Neru Loko before heading to Wairinding Hills to enjoy the savanna views at sunset. In the afternoon, check in at MYZE Waingapu and enjoy free time.',
                    'id' => 'Check-out dari Alamayah dan memulai perjalanan menuju Sumba Timur. Dalam perjalanan, tempat pertama yang dikunjungi adalah Air Terjun Lapopu yang berada di kawasan Taman Nasional Manupeu Tanah Daru, dengan jalur jalan kaki singkat menuju lokasi. Dilanjutkan dengan makan siang di Neru Loko sebelum menuju Bukit Wairinding untuk menikmati pemandangan savana saat matahari terbenam. Sore hari check-in di MYZE Waingapu dan waktu bebas untuk beristirahat.',
                    'es' => 'Check-out de Alamayah y comienzo del viaje hacia Sumba Este. En el camino, la primera parada es la cascada Lapopu, ubicada dentro del Parque Nacional Manupeu Tanah Daru, accesible con una caminata corta. Continúa con almuerzo en Neru Loko antes de dirigirse a las colinas de Wairinding para disfrutar de la sabana al atardecer. Por la tarde, check-in en MYZE Waingapu y tiempo libre.',
                ],
            ],
            [
                'day' => 7,
                'title' => ['en' => 'Canyon Waterfalls & Wild Savannas', 'id' => 'Air Terjun Ngarai & Savana Liar', 'es' => 'Cascadas del Cañón y Sabanas Salvajes'],
                'description' => [
                    'en' => 'Start the day with a visit to Tanggedu Waterfall, known for its canyon-like rock formations and a series of turquoise natural pools. The journey to the site passes through the wide Puru Kambera Savanna, with rolling hills and wild horses along the way. Continue to Puru Kambera Beach, a quiet stretch of white sand along the coast. Lunch is at Cemara Beach Resto before heading to Tenau Hills for sunset.',
                    'id' => 'Memulai hari dengan mengunjungi Air Terjun Tanggedu yang dikenal dengan aliran air di antara tebing batu menyerupai ngarai, dengan kolam alami berwarna biru kehijauan. Perjalanan menuju lokasi melewati Savana Puru Kambera yang luas dengan pemandangan perbukitan dan kuda liar. Dilanjutkan ke Pantai Puru Kambera, pantai berpasir putih yang tenang dan cocok untuk beristirahat sejenak. Makan siang dilakukan di Cemara Beach Resto sebelum menuju Bukit Tenau untuk menikmati pemandangan saat matahari terbenam.',
                    'es' => 'El día comienza con una visita a la cascada Tanggedu, conocida por sus formaciones rocosas tipo cañón y sus piscinas naturales de color turquesa. El trayecto pasa por la amplia sabana de Puru Kambera, con colinas onduladas y caballos salvajes. Continúa hacia Puru Kambera Beach, una playa tranquila de arena blanca. Almuerzo en Cemara Beach Resto antes de dirigirse a las colinas de Tenau para el atardecer.',
                ],
            ],
            [
                'day' => 8,
                'title' => ['en' => 'Jungle Pools & Dancing Mangroves', 'id' => 'Kolam Hutan & Dancing Mangroves', 'es' => 'Piscinas de la Selva y Manglares Danzantes'],
                'description' => [
                    'en' => 'Start the day with a visit to Piarakuku Hills, a scenic viewpoint overlooking the surrounding landscape. Continue to Waimarang Waterfall, a secluded jungle waterfall with a natural pool surrounded by rock cliffs. Lunch can be a picnic or at Walakiri Beach. In the afternoon, head to Walakiri Beach to enjoy the dancing mangroves at sunset.',
                    'id' => 'Memulai hari dengan mengunjungi Bukit Piarakuku, titik pandang dengan lanskap perbukitan yang terbuka dan cocok untuk menikmati suasana pagi. Dilanjutkan ke Air Terjun Waimarang, air terjun yang tersembunyi di dalam hutan dengan kolam alami yang dikelilingi tebing batu. Makan siang dapat dilakukan dengan lunch box atau di Pantai Walakiri. Sore hari menuju Pantai Walakiri untuk menikmati pemandangan dancing mangroves saat matahari terbenam.',
                    'es' => 'El día comienza con una visita a Piarakuku Hills, un mirador con amplias vistas del paisaje. Continúa hacia la cascada Waimarang, una cascada escondida en la selva con una piscina natural rodeada de acantilados. El almuerzo puede ser tipo picnic o en la playa de Walakiri. Por la tarde, traslado a Walakiri Beach para disfrutar de los manglares “danzantes” al atardecer.',
                ],
            ],
            [
                'day' => 9,
                'title' => ['en' => 'Cultural Heritage & Ikat Art', 'id' => 'Warisan Budaya & Seni Ikat', 'es' => 'Patrimonio Cultural y Arte Ikat'],
                'description' => [
                    'en' => 'Check out from MYZE Waingapu and begin the journey back toward West Sumba. The day starts with a stop at a local market to see fresh produce, spices, and traditional crafts such as woven textiles and handmade jewelry. Continue with a visit to observe the ikat weaving process, from natural dyeing using roots, bark, and plants to hand-weaving on traditional looms. After lunch, the journey continues to Prai Ijing Village, a traditional hilltop village with megalithic tombs and well-preserved clan houses. The day ends with check-in at SIMA Sumba in the afternoon.',
                    'id' => 'Check-out dari MYZE Waingapu dan memulai perjalanan kembali menuju Sumba Barat. Perjalanan diawali dengan pemberhentian di pasar lokal untuk melihat hasil bumi, rempah, serta kerajinan seperti kain tenun dan perhiasan. Dilanjutkan dengan melihat proses pembuatan tenun ikat, mulai dari pewarnaan benang menggunakan bahan alami hingga proses penenunan dengan alat tradisional. Setelah makan siang, perjalanan berlanjut ke Desa Prai Ijing, desa tradisional di atas bukit dengan kubur batu dan rumah adat yang masih terjaga. Lalu diakhiri dengan check-in di SIMA Sumba pada sore hari.',
                    'es' => 'Check-out de MYZE Waingapu y comienzo del viaje de regreso hacia Sumba Oeste. El día comienza con una parada en un mercado local para ver productos frescos, especias y artesanías como textiles y joyería hecha a mano. Continúa con la observación del proceso de tejido ikat, desde el teñido natural con raíces, corteza y plantas hasta el tejido a mano en telares tradicionales. Después del almuerzo, el viaje continúa hacia el pueblo de Prai Ijing, un pueblo tradicional en lo alto de una colina con tumbas megalíticas y casas tradicionales bien conservadas. El día termina con el check-in en SIMA Sumba por la tarde.',
                ],
            ],
            [
                'day' => 10,
                'title' => ['en' => 'Local Flavors & Departure', 'id' => 'Cita Rasa Lokal & Kepulangan', 'es' => 'Sabores Locales y Salida'],
                'description' => [
                    'en' => 'Visit Talasi Coffee & Cashew to see the local coffee, cashew production and enjoy the products. Continue with the transfer to Tambolaka Airport for departure.',
                    'id' => 'Mengunjungi Talasi Coffee & Cashew untuk melihat produksi kopi dan kacang lokal sekaligus menikmati hasil olahannya. Dilanjutkan dengan perjalanan menuju Bandara Tambolaka untuk kepulangan.',
                    'es' => 'Visita a Talasi Coffee & Cashew para ver la producción local de café y anacardos y disfrutar de sus productos. Luego traslado al aeropuerto de Tambolaka para la salida.',
                ],
            ],
        ];

        foreach ($itinerary as $index => $data) {
            $destination->itineraryItems()->create([
                'day_number' => $data['day'],
                'sort_order' => $index,
                'title' => $data['title'],
                'description' => $data['description'],
            ]);
        }
    }
}
