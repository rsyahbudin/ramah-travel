<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingTranslation extends Model
{
    protected $guarded = ['id'];

    /** @return BelongsTo<Setting, $this> */
    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }

    /** @return BelongsTo<Language, $this> */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
