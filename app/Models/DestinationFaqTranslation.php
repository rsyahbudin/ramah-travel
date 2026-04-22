<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DestinationFaqTranslation extends Model
{
    protected $guarded = ['id'];

    /** @return BelongsTo<DestinationFaq, $this> */
    public function faq(): BelongsTo
    {
        return $this->belongsTo(DestinationFaq::class, 'destination_faq_id');
    }

    /** @return BelongsTo<Language, $this> */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
