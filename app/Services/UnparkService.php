<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Enums\HourlyRate;
use App\Models\ParkingLot;
use App\Models\ParkingHistory;
use App\Enums\ParkingStatus;
use Carbon\Carbon;


class UnparkService
{
    /**
     * Calculate and record unparking details
     *
     * @param $parkingRecord
     * @param $endDatetime
     * 
     * @return ParkingHistory
     */
    public function payParkingRecord($parkingRecord, $endDatetime = false)
    {
        $calculateParking = $this->calculateParking($parkingRecord, $endDatetime);

        $parkingRecord->status         = ParkingStatus::COMPLETED;
        $parkingRecord->rate           = $calculateParking['rate'];
        $parkingRecord->total_hours    = $calculateParking['total_hours'];
        $parkingRecord->paid_hours     = $calculateParking['paid_hours'];
        $parkingRecord->balance_hours  = $calculateParking['balance_hours'];
        $parkingRecord->end_datetime   = $endDatetime;
        $parkingRecord->save();

        return $parkingRecord;
    }

    /**
     * Calculate Parking
     *
     * @param $parkingRecord
     * @param $endDatetime
     * 
     * @return array
     */
    public function calculateParking($parkingRecord, $endDatetime = false)
    {
        //Get rate, start date and end date info
        $rate = HourlyRate::$prices[$parkingRecord->slot_type];
        $startDate = Carbon::parse($parkingRecord->start_datetime);
        $endDate = (!$endDatetime)? Carbon::now() : Carbon::parse($endDatetime);

        //Get date difference in minutes
        $totalMinutes = $endDate->diffInMinutes($startDate, true);

        //Get difference in hours rounded up
        $totalHours = $totalMinutes / 60;

        //If parking record is subjected to continuous rate, calculate base on continuous parking
        //else use normal parking calculations
        if ($parkingRecord->isContinuousRate()) {
            $results = $this->calculateContinuousParking($parkingRecord, $rate, $totalHours);
        }
        else {
            $results = $this->calculateNormalParking($rate, $totalHours);
        }

        return $results;
    }

    /**
     * Calculate rate for continuous parking
     *
     * @param $parkingRecord
     * @param $rate
     * @param $totalHours
     * 
     * @return array
     */
    private function calculateContinuousParking($parkingRecord, $rate, $totalHours)
    {
        $totalRate = 0;

        ///Get previous parking info
        $previousParking = ParkingHistory::find($parkingRecord->continuous_rate_id);
        $balanceHours = $previousParking->balance_hours;

        //If there are balance hours left from the previous parking, 
        //subtract it to current total hours
        if ($balanceHours > 0) {
            if ($totalHours <= $balanceHours) {
                $balanceHours -= $totalHours;
                $totalHours = 0;
                
            }
            else{
                $totalHours -= $balanceHours;
                $balanceHours = 0;
            }
        }

        //Round remaining hours
        $remainingHours = ceil($totalHours);

        //If total hours excedeed 24 hrs, add whole day fee to total Rate
        if ($remainingHours >= 24) {
            $wholeDayHours = floor( $remainingHours / 24 );
            $remainingHours -= $wholeDayHours * 24;

            $totalRate += $wholeDayHours * HourlyRate::WHOLEDAY;
        }

        //Calculate normal rate
        $totalRate += $remainingHours * $rate;

        return [
            'rate'          => $totalRate, 
            'total_hours'   => $totalHours, 
            'paid_hours'    => ceil($totalHours),
            'balance_hours' => $balanceHours
        ];
    }

    /**
     * Calculate rate for normal parking
     *
     * @param $rate
     * @param $totalHours
     * 
     * @return array
     */
    private function calculateNormalParking($rate, $totalHours)
    {
        $totalRate = 0;
        $remainingHours = ceil($totalHours);
        $flatHours = config('parking.flat_hours');

        //If total hours excedeed 24 hrs, add whole day fee to total Rate
        //else, calculate flat rate
        if ($remainingHours >= 24) {
            $wholeDayHours = floor( $remainingHours / 24 );
            $remainingHours -= $wholeDayHours * 24;

            $totalRate += $wholeDayHours * HourlyRate::WHOLEDAY;
        }
        else{
            //Subtract 3 hours for the flat rate
            $remainingHours = ( $remainingHours > $flatHours )? $remainingHours - $flatHours : 0;
            $totalRate += HourlyRate::FLAT;
        }

        //Calculate normal rate
        $totalRate += $remainingHours * $rate;

        return [
            'rate' => $totalRate, 
            'total_hours' => $totalHours, 
            'paid_hours'  => ( $totalHours <= $flatHours )? $flatHours : ceil($totalHours),
            'balance_hours' => ( $totalHours <= $flatHours )? $flatHours - $totalHours : 0
        ];
    }
}
