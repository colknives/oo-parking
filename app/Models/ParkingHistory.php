<?php

namespace App\Models;

use App\Observers\ParkingHistoryObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingHistory extends Model
{
    use HasFactory;

    protected $table = 'parking_history';

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // register model observer
        static::observe(ParkingHistoryObserver::class);
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
