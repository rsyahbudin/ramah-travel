<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DestinationTripInfoTranslation extends Model
{
    protected $guarded = ['id'];

    /** @return BelongsTo<DestinationTripInfo, $this> */
    public function tripInfo(): BelongsTo
    {
        return $this->belongsTo(DestinationTripInfo::class, 'destination_trip_info_id');
    }

    /** @return BelongsTo<Language, $this> */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
