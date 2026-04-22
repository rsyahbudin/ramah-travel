<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslations
{
    /**
     * Get a translatable attribute's value.
     */
    public function getAttribute($key)
    {
        if (in_array($key, $this->translatable ?? [])) {
            return $this->getTranslation($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * Get the translation for the given key and locale.
     */
    public function getTranslation(string $key, ?string $locale = null)
    {
        $locale = $locale ?: App::getLocale();
        $translations = parent::getAttribute($key);

        if (! is_array($translations)) {
            // Handle case where it might be a string (e.g. not yet cast or empty)
            if (is_string($translations) && $translations !== '') {
                $decoded = json_decode($translations, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $translations = $decoded;
                } else {
                    return $translations;
                }
            } else {
                return $translations;
            }
        }

        // Return translation for requested locale, or fall back to English,
        // or finally the first available translation.
        return $translations[$locale]
            ?? $translations['en']
            ?? (count($translations) > 0 ? reset($translations) : '');
    }

    /**
     * Get all translations for the given key.
     */
    public function getTranslations(string $key): array
    {
        $translations = parent::getAttribute($key);

        if (is_string($translations)) {
            return json_decode($translations, true) ?: [];
        }

        return $translations ?: [];
    }
}
