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
     * Buffer for translations to be saved.
     */
    protected array $translationBuffer = [];

    /**
     * Boot the trait and hook into Eloquent events.
     */
    /**
     * Boot the trait and hook into Eloquent events.
     */
    protected static function bootHasTranslations(): void
    {
        static::saving(function ($model) {
            foreach ($model->getTranslatableFields() as $field) {
                if (array_key_exists($field, $model->attributes)) {
                    $value = $model->attributes[$field];

                    if (is_array($value)) {
                        // Move from attributes to buffer
                        foreach ($value as $locale => $translationValue) {
                            $model->translationBuffer[$locale][$field] = $translationValue;
                        }
                        unset($model->attributes[$field]);
                    }
                }
            }
        });

        static::saved(function ($model) {
            if (! empty($model->translationBuffer)) {
                $model->syncTranslations($model->translationBuffer);
                $model->translationBuffer = [];

                // Clear the relations so they are reloaded from DB
                $model->refreshTranslations();
            }
        });
    }

    /**
     * Get translatable fields for the model.
     */
    public function getTranslatableFields(): array
    {
        return $this->translatable ?? [];
    }

    /**
     * Override fill to capture translatable fields that might not be columns.
     */
    public function fill(array $attributes)
    {
        foreach ($this->getTranslatableFields() as $field) {
            if (array_key_exists($field, $attributes)) {
                $this->setAttribute($field, $attributes[$field]);
                unset($attributes[$field]);
            }
        }

        return parent::fill($attributes);
    }

    /**
     * Override getAttribute to handle translatable fields.
     */
    public function getAttribute($key)
    {
        if (in_array($key, $this->getTranslatableFields())) {
            return $this->getTranslation($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * Override setAttribute to handle translatable fields.
     */
    public function setAttribute($key, $value)
    {
        if (isset($this->translatable) && in_array($key, $this->translatable)) {
            if (is_array($value)) {
                // value is [locale => translation_value]
                foreach ($value as $locale => $translationValue) {
                    if (!isset($this->translationBuffer[$locale])) {
                        $this->translationBuffer[$locale] = [];
                    }
                    $this->translationBuffer[$locale][$key] = $translationValue;
                }
                return $this;
            }
        }

        return parent::setAttribute($key, $value);
    }

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
     * Cache for translations within the current request to avoid redundant processing.
     */
    protected array $relationTranslationCache = [];

    /**
     * Get translation for a specific field.
     */
    public function getTranslation(string $field, ?string $locale = null, bool $useFallback = true)
    {
        $locale ??= App::getLocale();
        $cacheKey = "{$locale}_{$field}";

        if (array_key_exists($cacheKey, $this->relationTranslationCache)) {
            return $this->relationTranslationCache[$cacheKey];
        }

        // Use loadMissing instead of load to ensure nested language is always checked
        $this->loadMissing('translations.language');

        // 1. Try specified locale
        $translation = $this->translations->first(function ($t) use ($locale) {
            return $t->language && $t->language->code === $locale;
        });

        $value = null;
        if ($translation && isset($translation->$field) && $translation->$field !== '') {
            $value = $translation->$field;
        }

        $this->relationTranslationCache[$cacheKey] = $value;

        return $value;
    }

    /**
     * Get all translations for a field, keyed by language code.
     *
     * @return array<string, string|null>
     */
    public function getTranslations(string $field): array
    {
        $this->loadMissing('translations.language');

        $results = [];
        foreach ($this->translations as $t) {
            $code = $t->language?->code ?? $t->language_id;
            if ($code) {
                $results[(string)$code] = $t->$field;
            }
        }

        return $results;
    }

    /**
     * Sync translations for all active languages.
     *
     * @param  array<string, array<string, string>>  $translationsData  Keyed by language code.
     */
    public function syncTranslations(array $translationsData): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($translationsData) {
            foreach ($translationsData as $code => $fields) {
                // Ensure the language exists
                $language = Language::where('code', $code)->first();

                if (! $language) {
                    continue;
                }

                // Security: Filter out any fields that are not explicitly marked as translatable
                $translatableFields = $this->getTranslatableFields();
                $dataToSave = array_intersect_key($fields, array_flip($translatableFields));

                if (! empty($dataToSave)) {
                    $this->translations()->updateOrCreate(
                        ['language_id' => $language->id],
                        $dataToSave
                    );
                }
            }
        });
    }

    /**
     * Clear translation relation cache and reload.
     */
    public function refreshTranslations(): self
    {
        $this->unsetRelation('translations');
        $this->unsetRelation('translation');
        return $this;
    }

    /**
     * Infer the translation model class name from the parent model.
     */
    protected function getTranslationModelClass(): string
    {
        return static::class.'Translation';
    }
}
