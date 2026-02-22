<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'destination_id',
        'name',
        'email',
        'phone',
        'travel_date',
        'person',
        'city',
        'country',
        'type',
        'status',
        'message',
    ];

    protected $casts = [
        'travel_date' => 'date',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
