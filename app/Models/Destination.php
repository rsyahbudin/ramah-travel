<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Destination extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    protected array $translatable = ['title', 'location', 'duration', 'theme', 'description', 'highlights'];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'price_max' => 'integer',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    /** @return HasMany<DestinationTranslation, $this> */
    public function translations(): HasMany
    {
        return $this->hasMany(DestinationTranslation::class);
    }

    /** @return HasOne<DestinationTranslation, $this> */
    public function translation(): HasOne
    {
        return $this->hasOne(DestinationTranslation::class)
            ->whereHas('language', fn ($q) => $q->where('code', app()->getLocale()));
    }

    /** @return HasMany<DestinationImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(DestinationImage::class);
    }

    /** @return HasMany<DestinationItineraryItem, $this> */
    public function itineraryItems(): HasMany
    {
        return $this->hasMany(DestinationItineraryItem::class)->orderBy('day_number')->orderBy('sort_order');
    }

    /** @return HasMany<DestinationItem, $this> */
    public function includeItems(): HasMany
    {
        return $this->hasMany(DestinationItem::class)
            ->where('type', 'include')
            ->orderBy('sort_order');
    }

    /** @return HasMany<DestinationItem, $this> */
    public function excludeItems(): HasMany
    {
        return $this->hasMany(DestinationItem::class)
            ->where('type', 'exclude')
            ->orderBy('sort_order');
    }

    /** @return HasMany<DestinationFaq, $this> */
    public function faqs(): HasMany
    {
        return $this->hasMany(DestinationFaq::class)->orderBy('sort_order');
    }

    /** @return HasMany<DestinationTripInfo, $this> */
    public function tripInfos(): HasMany
    {
        return $this->hasMany(DestinationTripInfo::class)->orderBy('sort_order');
    }

    /** @return HasMany<Booking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getPriceRangeAttribute(): string
    {
        if ($this->price_max) {
            return '$'.number_format((float) $this->price, 0).' - $'.number_format((float) $this->price_max, 0);
        }

        return '$'.number_format((float) $this->price, 0);
    }
}
