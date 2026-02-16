<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    protected $translatable = [
        'title',
        'description',
        'location',
        'duration',
        'theme',
        'highlights',
        'itinerary',
        'includes',
        'excludes',
        'faq',
        'trip_info',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_max' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_visible' => 'boolean',
        'highlights' => 'json',
        'itinerary' => 'json',
        'includes' => 'json',
        'excludes' => 'json',
        'faq' => 'json',
        'trip_info' => 'json',
    ];

    public function images()
    {
        return $this->hasMany(DestinationImage::class);
    }

    public function getPriceRangeAttribute()
    {
        if ($this->price_max) {
            return '$'.number_format($this->price, 0).' - $'.number_format($this->price_max, 0);
        }

        return '$'.number_format($this->price, 0);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
