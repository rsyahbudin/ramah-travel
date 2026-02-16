<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = ['id'];

    public static function getTranslated(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        $value = $setting->value;
        $decoded = json_decode($value, true);

        if (is_array($decoded)) {
            $locale = app()->getLocale();

            return $decoded[$locale] ?? $decoded['en'] ?? reset($decoded) ?? $default;
        }

        return $value ?: $default;
    }
}
