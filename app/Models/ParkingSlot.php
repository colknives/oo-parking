<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingSlot extends Model
{
    use HasFactory;

    protected $table = 'parking_slots';

    /*
     |--------------------------------------------------------------------------
     | Relationship
     |--------------------------------------------------------------------------
     */

    /**
     * Parking slot to parking lot relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parkingLot()
    {
        return $this->belongsTo(ParkingLot::class, 'parking_lot_id');
    }
}
