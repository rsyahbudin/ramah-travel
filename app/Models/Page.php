<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Page extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    /** @return HasMany<PageTranslation, $this> */
    public function translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }

    /** @return HasOne<PageTranslation, $this> */
    public function translation(): HasOne
    {
        return $this->hasOne(PageTranslation::class)
            ->whereHas('language', fn ($q) => $q->where('code', app()->getLocale()));
    }

    /** @return HasMany<PageSection, $this> */
    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }
}
