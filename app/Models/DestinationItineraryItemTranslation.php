<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DestinationItineraryItemTranslation extends Model
{
    protected $guarded = ['id'];

    /** @return BelongsTo<DestinationItineraryItem, $this> */
    public function itineraryItem(): BelongsTo
    {
        return $this->belongsTo(DestinationItineraryItem::class, 'destination_itinerary_item_id');
    }

    /** @return BelongsTo<Language, $this> */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
