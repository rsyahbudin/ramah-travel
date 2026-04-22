<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageSectionFeature extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    /** @return BelongsTo<PageSection, $this> */
    public function pageSection(): BelongsTo
    {
        return $this->belongsTo(PageSection::class);
    }

    /** @return HasMany<PageSectionFeatureTranslation, $this> */
    public function translations(): HasMany
    {
        return $this->hasMany(PageSectionFeatureTranslation::class);
    }
}
