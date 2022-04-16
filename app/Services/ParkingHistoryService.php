<?php

namespace App\Services;

use Illuminate\Support\Arr;
use App\Models\ParkingLot;
use App\Models\ParkingHistory;
use App\Enums\ParkingStatus;
use Carbon\Carbon;

class ParkingHistoryService
{
    protected $entryPoint;

    public function __construct($entryPoint = false)
    {
        $this->entryPoint = $entryPoint;
    }

    /**
     * Check parking vacancy
     *
     * @param ParkingLot $parkingLot
     * @param $size
     * @param $entryPoint
     * 
     * @return boolean
     */
    public function checkVacancy(ParkingLot $parkingLot, $vehicleSize)
    {
        //Get all ongoing parking records
        $occupied = ParkingHistory::where('parking_lot_id', $parkingLot->id)
            ->where('status', ParkingStatus::ONGOING)
            ->get();

        //If all slots are occupied, return false
        if (count($occupied) >= $parkingLot->total_slot) {
            return false;
        }

        //Get all available slots for provided vehicle
        $slots = $this->getAvailableSlots(
            $occupied, $parkingLot->parkingSlots, $vehicleSize);

        return count($slots) > 0;
    }

    /**
     * Check if vehicle is legible for continuous rate
     *
     * @param $licensePlate
     * @param $startDate
     * @param $entryPoint
     * 
     * @return boolean
     */
    public function checkContinuousRate($licensePlate, $startDate = false)
    {
        if (!$startDate) {
            $startDate = Carbon::now();
        }

        $continuousRate = ParkingHistory::where('license_plate', $licensePlate)
            ->where('status', ParkingStatus::COMPLETED)
            ->where('end_datetime', '>=', $startDate->subMinutes(30))
            ->orderBy('end_datetime', 'DESC')
            ->first();

        if (!$continuousRate) {
            return false;
        }

        return $continuousRate;
    }

    /**
     * Park vehicle
     *
     * @param ParkingLot $parkingLot
     * @param $size
     * @param $entryPoint
     * 
     * @return boolean
     */
    public function parkVehicle(ParkingLot $parkingLot, $data)
    {
        //Get all ongoing parking records
        $occupied = ParkingHistory::where('parking_lot_id', $parkingLot->id)
            ->where('status', ParkingStatus::ONGOING)
            ->get();

        //Get all available slots for provided vehicle
        $slot = $this->getFirstSlot(
            $occupied, $parkingLot->parkingSlots, $data['vehicle_size']);

        //If no start date provided, base it on current datetime
        $startDate = ( $data['start_datetime'] )? 
            Carbon::parse($data['start_datetime']) : Carbon::now();

        //Check if vehicle is legible for continuous rate
        $continuousHistory = $this->checkContinuousRate($data['license_plate'], $startDate);

        $history = new ParkingHistory;
        $history->parking_lot_id  = $slot['parking_lot_id'];
        $history->parking_slot_id = $slot['id'];
        $history->license_plate   = $data['license_plate'];
        $history->vehicle_size    = $data['vehicle_size'];
        $history->status          = ParkingStatus::ONGOING;
        $history->start_datetime  = $startDate;

        //If continuous rate is activated, base start date on previous parking details
        if ($continuousHistory) {
            $history->continuous_rate = true;
            $history->start_datetime = $continuousHistory->start_datetime;
        }

        $history->save();

        return $history;
    }

    /**
     * Get first available slot
     *
     * @param $history
     * @param $slots
     * @param $vehicleSize
     * 
     * @return array
     */
    private function getFirstSlot($history, $slots, $vehicleSize)
    {
        $slots = $this->getAvailableSlots(
            $history, $slots, $vehicleSize);

        return $slots[0];
    }

    /**
     * Get available parking slots
     *
     * @param $history
     * @param $slots
     * @param $vehicleSize
     * 
     * @return array
     */
    private function getAvailableSlots($history, $slots, $vehicleSize)
    {
        //Get all occupied parking slots
        $occupiedSlots = Arr::pluck($history, 'parking_slot_id');

        //convert slot collection to array for manipulation
        $slots = $slots->toArray();

        //Filter unused slots
        $slots = Arr::where($slots, function ($value, $key) use ($occupiedSlots) {
            return !in_array($value['id'], $occupiedSlots);
        });

        //Filter all unoccupied slots base on vehicle size
        $slots = array_filter($slots, function ($value) use ($vehicleSize) {
            return $value['type']  >= $vehicleSize;
        });

        //Sort nearest parking slot
        usort($slots, "self::compareSlotDistance");

        return $slots;
    }

    /**
     * Compare distance of provided parking slots
     *
     * @param $history
     * @param $slots
     * @param $vehicleSize
     * 
     * @return array
     */
    private function compareSlotDistance($slotA, $slotB)
    {
        $distanceA = $slotA['distance'][$this->entryPoint];
        $distanceB = $slotB['distance'][$this->entryPoint];

        return $distanceA > $distanceB;
    }
}
