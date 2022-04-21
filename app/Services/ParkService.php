<?php

namespace App\Services;

use App\Enums\HourlyRate;
use Illuminate\Support\Arr;
use App\Models\ParkingLot;
use App\Models\ParkingHistory;
use App\Enums\ParkingStatus;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class ParkService
{
    protected $entryPoint;

    /**
     * Initialize park service
     *
     * @param $entryPoint
     */
    public function __construct($entryPoint = false)
    {
        $this->entryPoint = $entryPoint;
    }

    /**
     * Check parking vacancy
     *
     * @param ParkingLot $parkingLot
     * @param $vehicleSize
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
     * Check if already parked
     *
     * @param ParkingLot $parkingLot
     * @param $licensePlate
     * 
     * @return boolean
     */
    public function checkIfVehicleParked(ParkingLot $parkingLot, $licensePlate)
    {
        //Get all ongoing parking records
        $parked = ParkingHistory::where('parking_lot_id', $parkingLot->id)
            ->where('license_plate', $licensePlate)
            ->where('status', ParkingStatus::ONGOING)
            ->get();

        return count($parked) > 0;
    }

    /**
     * Check if vehicle is legible for continuous rate
     *
     * @param $licensePlate
     * @param $startDate
     * 
     * @return ParkingHistory | boolean
     */
    public function checkContinuousRate($licensePlate, $startDate = false)
    {
        $startDate = (!$startDate)? Carbon::now() : Carbon::parse($startDate);

        $continuousRate = ParkingHistory::where('license_plate', $licensePlate)
            ->where('status', ParkingStatus::COMPLETED)
            ->where('end_datetime', '>=', $startDate->subHour(1))
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
     * @param $data
     * 
     * @return ParkingHistory | boolean
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

        //Check if vehicle is legible for continuous rate
        $continuousHistory = $this->checkContinuousRate($data['license_plate'], $data['start_datetime']);

        $history = new ParkingHistory;
        $history->parking_lot_id  = $slot['parking_lot_id'];
        $history->parking_slot_id = $slot['id'];
        $history->license_plate   = $data['license_plate'];
        $history->vehicle_size    = $data['vehicle_size'];
        $history->slot_type       = $slot['type'];
        $history->status          = ParkingStatus::ONGOING;
        $history->start_datetime  = $data['start_datetime'];

        //If continuous rate is activated, base start date on previous parking details
        if ($continuousHistory) {
            $history->continuous_rate_id = $continuousHistory->id;
            $history->start_datetime = $continuousHistory->end_datetime;
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
     * @param $slotA
     * @param $slotB
     * 
     * @return boolean
     */
    private function compareSlotDistance($slotA, $slotB)
    {
        $distanceA = $slotA['distance'][$this->entryPoint];
        $distanceB = $slotB['distance'][$this->entryPoint];

        return ( $distanceA > $distanceB );
    }
}
