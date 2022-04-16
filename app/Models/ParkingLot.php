<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingLot extends Model
{
    use HasFactory;

    protected $table = 'parking_lots';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_datetime' => 'datetime',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationship
     |--------------------------------------------------------------------------
     */

    /**
     * Parking lot to parking slots relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parkingSlots()
    {
        return $this->hasMany(ParkingSlot::class, 'parking_lot_id');
    }
}
