<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setting extends Model
{
    protected $guarded = ['id'];

    protected static ?\Illuminate\Support\Collection $cache = null;

    /**
     * Preload all settings with their translations into memory once per request.
     */
    public static function loadCache(): void
    {
        if (static::$cache === null) {
            static::$cache = static::with('translationsRelation.language')->get();
        }
    }

    /** @return HasMany<SettingTranslation, $this> */
    public function translationsRelation(): HasMany
    {
        return $this->hasMany(SettingTranslation::class);
    }

    /**
     * Get a setting value (plain or translated, based on type).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        static::loadCache();

        $setting = static::$cache->firstWhere('key', $key);

        if (! $setting) {
            return $default;
        }

        if ($setting->type === 'translatable') {
            return $setting->getTranslatedValue() ?? $default;
        }

        if ($setting->type === 'json') {
            return is_string($setting->value) ? json_decode($setting->value, true) : ($setting->value ?? $default);
        }

        return $setting->value ?? $default;
    }

    /**
     * Get the translated value for the active locale with fallback.
     */
    public function getTranslatedValue(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        $translation = $this->translationsRelation
            ->first(fn ($t) => $t->language?->code === $locale);

        if ($translation) {
            return $translation->value;
        }

        // Fallback to English
        $fallback = $this->translationsRelation
            ->first(fn ($t) => $t->language?->code === 'en');

        return $fallback?->value ?? $this->translationsRelation->first()?->value;
    }

    /**
     * Get all translated values keyed by language code.
     *
     * @return array<string, string>
     */
    public function getAllTranslations(): array
    {
        return $this->translationsRelation->mapWithKeys(
            fn ($t) => [$t->language?->code ?? $t->language_id => $t->value]
        )->toArray();
    }

    /**
     * Sync translated values for all provided language codes.
     *
     * @param  array<string, string>  $values  Keyed by language code.
     */
    public function syncTranslations(array $values): void
    {
        $languages = Language::whereIn('code', array_keys($values))
            ->where('is_active', true)
            ->get()
            ->keyBy('code');

        foreach ($values as $code => $value) {
            if (! isset($languages[$code])) {
                continue;
            }

            $this->translationsRelation()->updateOrCreate(
                ['language_id' => $languages[$code]->id],
                ['value' => $value]
            );
        }
    }

    /**
     * Backward-compatible helper using the new relational approach.
     */
    public static function getTranslated(string $key, mixed $default = null): mixed
    {
        return self::get($key, $default);
    }
}
