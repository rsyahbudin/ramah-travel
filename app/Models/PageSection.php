<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\HasTranslatedMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageSection extends Model
{
    use HasTranslations, HasTranslatedMeta;

    protected $guarded = ['id'];

    protected array $translatable = ['title', 'subtitle', 'content', 'cta_text'];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'is_visible' => 'boolean',
        ];
    }

    /** @return BelongsTo<Page, $this> */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /** @return HasMany<PageSectionTranslation, $this> */
    public function translations(): HasMany
    {
        return $this->hasMany(PageSectionTranslation::class);
    }

    /** @return HasMany<PageSectionFeature, $this> */
    public function features(): HasMany
    {
        return $this->hasMany(PageSectionFeature::class)->orderBy('sort_order');
    }
}
