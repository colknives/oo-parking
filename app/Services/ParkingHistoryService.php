<?php

namespace App\Services;

use App\Models\ParkingLot;
use App\Models\ParkingHistory;
use App\Enums\ParkingStatus;

class ParkingHistoryService
{
    /**
     * Check parking vacancy
     *
     * @param ParkingLot $parkingLot
     * @param $size
     * @param $entryPoint
     * @return boolean
     */
    public function checkVacancy(ParkingLot $parkingLot, $size, $entryPoint)
    {
        //Get all ongoing parking records
        $occupiedCount = ParkingHistory::where('parking_lot_id', $parkingLot->id)
            ->where('status', ParkingStatus::ONGOING)
            ->count();
    }
}
