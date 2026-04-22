<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    protected $guarded = ['id'];

    /** @return HasMany<DestinationTranslation, $this> */
    public function destinationTranslations(): HasMany
    {
        return $this->hasMany(DestinationTranslation::class);
    }

    /** @return HasMany<PageTranslation, $this> */
    public function pageTranslations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }

    /** @return HasMany<SettingTranslation, $this> */
    public function settingTranslations(): HasMany
    {
        return $this->hasMany(SettingTranslation::class);
    }
}
