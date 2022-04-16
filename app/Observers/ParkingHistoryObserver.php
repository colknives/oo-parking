<?php

namespace App\Observers;

use App\Models\ParkingHistory;
use Carbon\Carbon;

class ParkingHistoryObserver
{
    /**
     * Handle the user "creating" event.
     *
     * @param  \App\Models\ParkingHistory  $parkingHistory
     * @return void
     */
    public function creating(ParkingHistory $parkingHistory)
    {
        $parkingHistory->start_datetime = Carbon::parse($parkingHistory->start_datetime)
            ->format('Y-m-d h:i:s');
    }

    /**
     * Handle the user "updating" event.
     *
     * @param  \App\Models\ParkingHistory  $parkingHistory
     * @return void
     */
    public function updating(ParkingHistory $parkingHistory)
    {
        $parkingHistory->start_datetime = Carbon::parse($parkingHistory->start_datetime)
            ->format('Y-m-d h:i:s');
        $parkingHistory->end_datetime = Carbon::parse($parkingHistory->end_datetime)
            ->format('Y-m-d h:i:s');
    }
}
