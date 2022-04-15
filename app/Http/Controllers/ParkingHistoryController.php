<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ParkVehicleRequest;
use App\Services\ParkingHistoryService;
use App\Models\ParkingLot;

class ParkingHistoryController extends Controller
{
    /**
     * Park vehicle
     *
     * @param ParkingLot $parkingLot
     * @param ParkVehicleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function park(
        ParkVehicleRequest $request,
        ParkingLot $parkingLot,
        ParkingHistoryService $parkingHistoryService)
    {
        $data = $request->all();

        //Check if there is still slot for provided parking lot and vehicle
        if ($parkingHistoryService->checkVacancy(
                $parkingLot, 
                $data['size'], 
                $data['entry_point'])) {

        }


    }
}
