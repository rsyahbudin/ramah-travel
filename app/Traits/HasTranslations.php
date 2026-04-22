<?php

namespace App\Traits;

use App\Models\Language;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\App;

/**
 * Provides Eloquent relation-based translation for models.
 *
 * Requirements:
 *  - Model must have a `translations()` HasMany relation to its translation model.
 *  - Translation model must have a `language_id` FK and a `language()` BelongsTo.
 *  - Protected $translatableFields array must define which fields are translatable.
 */
trait HasTranslations
{
    /**
     * Get all translations for this model.
     *
     * @return HasMany<\Illuminate\Database\Eloquent\Model, $this>
     */
    abstract public function translations(): HasMany;

    /**
     * Get translation record for the currently active locale.
     *
     * @return HasOne<\Illuminate\Database\Eloquent\Model, $this>
     */
    public function translation(): HasOne
    {
        $locale = App::getLocale();

        return $this->hasOne($this->getTranslationModelClass())
            ->whereHas('language', fn ($q) => $q->where('code', $locale));
    }

    /**
     * Get translation record for a specific locale code.
     *
     * @return HasOne<\Illuminate\Database\Eloquent\Model, $this>
     */
    public function translationFor(string $localeCode): HasOne
    {
        return $this->hasOne($this->getTranslationModelClass())
            ->whereHas('language', fn ($q) => $q->where('code', $localeCode));
    }

    /**
     * Get a translated field value for the active locale with fallback.
     */
    public function getTranslation(string $field, ?string $locale = null): ?string
    {
        $locale ??= App::getLocale();

        $translation = $this->translations
            ->first(fn ($t) => $t->language?->code === $locale);

        if ($translation && isset($translation->$field)) {
            return $translation->$field;
        }

        // Fallback: try English
        $fallback = $this->translations
            ->first(fn ($t) => $t->language?->code === 'en');

        if ($fallback && isset($fallback->$field)) {
            return $fallback->$field;
        }

        // Last resort: first available translation
        $first = $this->translations->first();

        return $first?->$field ?? null;
    }

    /**
     * Get all translations for a field, keyed by language code.
     *
     * @return array<string, string|null>
     */
    public function getTranslations(string $field): array
    {
        return $this->translations->mapWithKeys(
            fn ($t) => [$t->language?->code ?? $t->language_id => $t->$field]
        )->toArray();
    }

    /**
     * Sync translations for all active languages.
     *
     * @param  array<string, array<string, string>>  $translationsData  Keyed by language code.
     */
    public function syncTranslations(array $translationsData): void
    {
        $languages = Language::whereIn('code', array_keys($translationsData))
            ->where('is_active', true)
            ->get()
            ->keyBy('code');

        foreach ($translationsData as $code => $fields) {
            if (! isset($languages[$code])) {
                continue;
            }

            $this->translations()->updateOrCreate(
                ['language_id' => $languages[$code]->id],
                $fields
            );
        }
    }

    /**
     * Infer the translation model class name from the parent model.
     */
    protected function getTranslationModelClass(): string
    {
        return static::class.'Translation';
    }
}
