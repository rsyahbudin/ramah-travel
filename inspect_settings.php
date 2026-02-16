<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

$keys = ['about_title', 'about_content', 'about_label', 'about_stat_number', 'about_stat_text', 'experience_tiers_points'];

foreach ($keys as $key) {
    $s = Setting::where('key', $key)->first();
    if ($s) {
        echo $key.': '.$s->value.PHP_EOL;
        $decoded = json_decode($s->value, true);
        echo $key.' (decoded type): '.gettype($decoded).PHP_EOL;
    } else {
        echo $key.': NOT FOUND'.PHP_EOL;
    }
}
