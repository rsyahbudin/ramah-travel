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

        if (is_string($translations)) {
            $translations = json_decode($translations, true) ?: [];
        }

        if (! is_array($translations)) {
            return $translations;
        }

        // Return the requested locale, or fallback to English, or return the first available translation
        return $translations[$locale] ?? $translations['en'] ?? (count($translations) > 0 ? reset($translations) : null);
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

        return is_array($translations) ? $translations : [];
    }
}
