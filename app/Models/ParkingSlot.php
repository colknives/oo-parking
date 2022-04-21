<?php

namespace App\Models;

use App\Enums\ParkingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ParkingSlot extends Model
{
    use HasFactory;

    protected $table = 'parking_slots';

    /**
     * Interact with the parking slot's distance.
     *
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function distance(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

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

    /**
     * Parking slot to parking historry relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parkingHistory()
    {
        return $this->hasMany(ParkingHistory::class, 'parking_slot_id');
    }

    /**
     * Parking slot to parking historry relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ongoingParking()
    {
        return $this->hasMany(ParkingHistory::class, 'parking_slot_id')
            ->where('status', ParkingStatus::ONGOING)
            ->orderBy('start_datetime');
    }
}
