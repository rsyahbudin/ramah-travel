<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DestinationItemTranslation extends Model
{
    protected $guarded = ['id'];

    /** @return BelongsTo<DestinationItem, $this> */
    public function item(): BelongsTo
    {
        return $this->belongsTo(DestinationItem::class, 'destination_item_id');
    }

    /** @return BelongsTo<Language, $this> */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
