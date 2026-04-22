<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DestinationFaq extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    /** @return BelongsTo<Destination, $this> */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    /** @return HasMany<DestinationFaqTranslation, $this> */
    public function translations(): HasMany
    {
        return $this->hasMany(DestinationFaqTranslation::class);
    }
}
