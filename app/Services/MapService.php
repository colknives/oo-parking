<?php

namespace App\Services;

use App\Enums\HourlyRate;
use Illuminate\Support\Arr;
use App\Models\ParkingLot;
use App\Models\ParkingHistory;
use App\Enums\ParkingStatus;
use App\Models\ParkingSlot;
use Carbon\Carbon;

class MapService
{
    protected $parkingLot;

    /**
     * Initialize map service
     *
     * @param $parkingLot
     */
    public function __construct(ParkingLot $parkingLot)
    {
        $this->parkingLot = $parkingLot;
    }

    /**
     * Get parking lot map information
     * 
     * @return ParkingSlot
     */
    public function getParkingLotMap()
    {
        $map = ParkingSlot::with('ongoingParking')
                ->where('parking_lot_id', $this->parkingLot->id)
                ->get();

        return $map;
    }
}
