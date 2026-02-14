<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
        'price_max' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_visible' => 'boolean',
        'highlights' => 'array',
    ];

    public function images()
    {
        return $this->hasMany(DestinationImage::class);
    }

    public function getPriceRangeAttribute()
    {
        if ($this->price_max) {
            return '$' . number_format($this->price, 0) . ' - $' . number_format($this->price_max, 0);
        }

        return '$' . number_format($this->price, 0);
    }
}
