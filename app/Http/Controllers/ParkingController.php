<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Requests\ParkVehicleRequest;
use App\Http\Requests\UnparkVehicleRequest;
use App\Http\Resources\ParkingHistoryResource;
use App\Http\Resources\ParkingLotMapResource;
use App\Services\ParkService;
use App\Models\ParkingLot;
use App\Models\ParkingHistory;
use App\Services\MapService;
use App\Services\UnparkService;

class ParkingController extends Controller
{
    /**
     * Park vehicle
     *
     * @param ParkVehicleRequest $request
     * @param ParkingLot $parkingLot
     * 
     * @return \Illuminate\Http\Response
     */
    public function map(
        Request $request,
        ParkingLot $parkingLot)
    {
        //Initialize parking history service
        $parkService = new MapService($parkingLot);
        $map = $parkService->getParkingLotMap();

        return ParkingLotMapResource::collection($map);
    }

    /**
     * Park vehicle
     *
     * @param ParkVehicleRequest $request
     * @param ParkingLot $parkingLot
     * 
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
        $parkService = new ParkService($data['entry_point']);

        $exist = $parkService->checkIfVehicleParked(
            $parkingLot, 
            $data['license_plate']);

        //If vehicle already parked
        if ($exist) {
            return response()->json([
                'message'    => __('responses.parking.vehicle_parked')
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //Check if parking will cause conflicts with it's current parking records
        $conflict = $parkService->checkParkConflict(
            $parkingLot, 
            $data['license_plate'],
            $data['start_datetime']
        );

        //If details has conflict with it's other parking record
        if ($conflict) {
            return response()->json([
                'message'    => __('responses.parking.parking_conflict')
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $vacant = $parkService->checkVacancy(
            $parkingLot, 
            $data['vehicle_size']);

        //If no vacancy available
        if (!$vacant) {
            return response()->json([
                'message'    => __('responses.parking.no_vacant')
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //Park vehicle to available slot
        $park = $parkService->parkVehicle($parkingLot, $data);

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
     * @param UnparkVehicleRequest $request
     * @param ParkingHistory $parkingHistory
     * 
     * @return \Illuminate\Http\Response
     */
    public function unpark(
        UnparkVehicleRequest $request,
        ParkingHistory $parkingHistory)
    {
        $data = $request->all();

        //Initialize parking history service
        $unparkService = new UnparkService();

        //Check if unparking details will cause conflicts with it's current parking records
        $conflict = $unparkService->checkUnparkConflict(
            $parkingHistory, 
            $data['end_datetime']
        );

        //If details has conflict with it's other parking record
        if ($conflict) {
            return response()->json([
                'message'    => __('responses.parking.parking_conflict')
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //Calculate and unpark vehicle
        $payParkingRecord = $unparkService->payParkingRecord($parkingHistory, $data['end_datetime']);

        return new ParkingHistoryResource($payParkingRecord);
    }
}
