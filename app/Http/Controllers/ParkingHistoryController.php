<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\ParkVehicleRequest;
use App\Http\Resources\ParkingHistoryResource;
use App\Services\ParkingHistoryService;
use App\Models\ParkingLot;

class ParkingHistoryController extends Controller
{
    /**
     * Park vehicle
     *
     * @param ParkVehicleRequest $request
     * @param ParkingLot $parkingLot
     * @return \Illuminate\Http\Response
     */
    public function park(
        ParkVehicleRequest $request,
        ParkingLot $parkingLot)
    {
        $data = $request->all();

        //If entry point not exist
        if ($data['entry_point'] >= $parkingLot->total_entry) {
            return response()->json([
                'message'    => __('responses.parking.entry_not_exist')
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //Initialize parking history service
        $parkingHistoryService = new ParkingHistoryService($data['entry_point']);

        $vacant = $parkingHistoryService->checkVacancy(
            $parkingLot, 
            $data['vehicle_size']);

        //If no vacancy available
        if (!$vacant) {
            return response()->json([
                'message'    => __('responses.parking.no_vacant')
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //Park vehicle to available slot
        $park = $parkingHistoryService->parkVehicle($parkingLot, $data);

        if (!$park) {
            return response()->json([
                'message'    => __('responses.parking.parking_failed')
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new ParkingHistoryResource($park);
    }

    /**
     * Unpark vehicle
     *
     * @param ParkVehicleRequest $request
     * @param ParkingLot $parkingLot
     * @return \Illuminate\Http\Response
     */
    public function unpark(
        ParkVehicleRequest $request,
        ParkingLot $parkingLot)
    {
        $data = $request->all();

        //If entry point not exist
        if ($data['entry_point'] >= $parkingLot->total_entry) {
            return response()->json([
                'message'    => __('responses.parking.entry_not_exist')
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //Initialize parking history service
        $parkingHistoryService = new ParkingHistoryService($data['entry_point']);

        $vacant = $parkingHistoryService->checkVacancy(
            $parkingLot, 
            $data['vehicle_size']);

        //If no vacancy available
        if (!$vacant) {
            return response()->json([
                'message'    => __('responses.parking.no_vacant')
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //Park vehicle to available slot
        $park = $parkingHistoryService->parkVehicle($parkingLot, $data);

        if (!$park) {
            return response()->json([
                'message'    => __('responses.parking.parking_failed')
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new ParkingHistoryResource($park);
    }
}
