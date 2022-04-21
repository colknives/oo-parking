<?php

namespace App\Models;

use App\Observers\ParkingHistoryObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ParkingHistory extends Model
{
    use HasFactory;

    protected $table = 'parking_history';

    /**
     * Check if parking history has continuous rate.
     *
     * @return boolean
     */
    public function isContinuousRate() : bool
    {
        return filled($this->continuous_rate_id);
    }

    /*
     |--------------------------------------------------------------------------
     | Relationship
     |--------------------------------------------------------------------------
     */

    /**
     * Parking history to parking lot relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parkingLot()
    {
        return $this->belongsTo(ParkingLot::class, 'parking_lot_id');
    }

    /**
     * Parking history to parking slot relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parkingSlot()
    {
        return $this->belongsTo(ParkingLot::class, 'parking_slot_id');
    }
}
