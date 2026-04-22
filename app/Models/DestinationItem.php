<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DestinationItem extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    protected array $translatable = ['label'];

    /** @return BelongsTo<Destination, $this> */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    /** @return HasMany<DestinationItemTranslation, $this> */
    public function translations(): HasMany
    {
        return $this->hasMany(DestinationItemTranslation::class);
    }
}
