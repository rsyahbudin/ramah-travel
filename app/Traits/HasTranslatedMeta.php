<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

/**
 * Trait for models that have a JSON 'meta' column containing multilingual data.
 */
trait HasTranslatedMeta
{
    /**
     * Get a translated value from the meta JSON column.
     */
    public function getTranslatedMeta(string $key, ?string $locale = null): mixed
    {
        $locale ??= App::getLocale();
        $meta = $this->meta ?? [];

        // 1. Try specified locale
        if (isset($meta[$key][$locale]) && $meta[$key][$locale] !== '') {
            return $meta[$key][$locale];
        }

        // 2. Fallback to English
        if ($locale !== 'en' && isset($meta[$key]['en']) && $meta[$key]['en'] !== '') {
            return $meta[$key]['en'];
        }

        // 3. Last resort: Return first available or null
        if (isset($meta[$key]) && is_array($meta[$key])) {
            $first = collect($meta[$key])->filter(fn($v) => !empty($v))->first();
            return $first ?? null;
        }

        return null;
    }

    /**
     * Update multilingual meta values while preserving existing ones in other languages.
     * 
     * @param array $data Format: ['cta_text' => ['en' => '...', 'id' => '...'], ...]
     */
    public function updateTranslatedMeta(array $data): void
    {
        $meta = $this->meta ?? [];

        foreach ($data as $key => $translations) {
            if (!is_array($translations)) {
                // If it's a single value, we treat it as non-multilingual meta
                $meta[$key] = $translations;
                continue;
            }

            // Ensure the key exists in meta
            if (!isset($meta[$key]) || !is_array($meta[$key])) {
                $meta[$key] = [];
            }

            // Merge new translations with existing ones
            foreach ($translations as $locale => $value) {
                $meta[$key][$locale] = $value;
            }
        }

        $this->meta = $meta;
    }
}
