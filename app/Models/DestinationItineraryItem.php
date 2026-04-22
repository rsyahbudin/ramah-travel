<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DestinationItineraryItem extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    protected array $translatable = ['title', 'description'];

    /** @return BelongsTo<Destination, $this> */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    /** @return HasMany<DestinationItineraryItemTranslation, $this> */
    public function translations(): HasMany
    {
        return $this->hasMany(DestinationItineraryItemTranslation::class);
    }
}
