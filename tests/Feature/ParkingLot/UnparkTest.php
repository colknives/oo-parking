<?php

namespace Tests\Feature\ParkingHistory;

use App\Enums\ParkingSlotType;
use App\Enums\ParkingStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\ParkingLot;
use App\Models\ParkingSlot;
use App\Models\ParkingHistory;
use App\Enums\VehicleSize;
use Carbon\Carbon;

class UnparkTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Successfully unpark vehicle.
     *
     * @return void
     */
    public function test_success_small_vehicle_response()
    {
        //Generate test data
        $parkingLot = ParkingLot::factory()->create();
        $parkingSlot = ParkingSlot::factory()->create([
            'parking_lot_id' => $parkingLot->id,
            'type'           => ParkingSlotType::SMALL
        ]);

        $parkingHistory = ParkingHistory::factory()->create([
            'parking_lot_id'  => $parkingLot->id,
            'parking_slot_id' => $parkingSlot->id,
            'license_plate'   => 'abc123',
            'vehicle_size'    => VehicleSize::SMALL,
            'slot_type'       => ParkingSlotType::SMALL,
            'status'          => ParkingStatus::ONGOING,
            'rate'            => 0.00,
            'start_datetime'  => '2022-04-20 13:00:00'
        ]);

        $this->json('POST', route('parking.unpark', [
                'parkingHistory' => $parkingHistory->id
            ]), [
                'end_datetime'=> '2022-04-20 18:20:27'
            ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'parking_lot_id', 
                    'parking_slot_id', 
                    'license_plate', 
                    'vehicle_size', 
                    'status',
                    'rate',
                    'continuous_rate',
                    'start_datetime',
                    'end_datetime'
                ]
            ]);
    }

    /**
     * Successfully unpark vehicle with continuous rate.
     *
     * @return void
     */
    public function test_success_small_vehicle_continuous_response()
    {
        //Generate test data
        $parkingLot = ParkingLot::factory()->create();
        $parkingSlot = ParkingSlot::factory()->create([
            'parking_lot_id' => $parkingLot->id,
            'type'           => ParkingSlotType::SMALL
        ]);

        $previousParking = ParkingHistory::factory()->create([
            'parking_lot_id'  => $parkingLot->id,
            'parking_slot_id' => $parkingSlot->id,
            'license_plate'   => 'abc123',
            'vehicle_size'    => VehicleSize::SMALL,
            'slot_type'       => ParkingSlotType::SMALL,
            'status'          => ParkingStatus::COMPLETED,
            'rate'            => 40.00,
            'total_hours'     => 1.5,
            'paid_hours'      => 3,
            'start_datetime'  => '2022-04-18 13:00:00',
            'end_datetime'    => '2022-04-18 14:30:00'
        ]);


        ParkingHistory::factory()->create([
            'parking_lot_id'     => $parkingLot->id,
            'parking_slot_id'    => $parkingSlot->id,
            'continuous_rate_id' => $previousParking->id,
            'license_plate'      => 'abc123',
            'vehicle_size'       => VehicleSize::SMALL,
            'slot_type'          => ParkingSlotType::SMALL,
            'status'             => ParkingStatus::ONGOING,
            'rate'               => 0.00,
            'start_datetime'     => '2022-04-18 14:30:00'
        ]);

        $this->json('POST', route('parking.unpark', [
                'parkingLot' => $parkingLot->id
            ]), [
                'license_plate' => 'abc123',
                'end_datetime'=> '2022-04-19 16:30:00'
            ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'parking_lot_id', 
                    'parking_slot_id', 
                    'license_plate', 
                    'vehicle_size', 
                    'status',
                    'rate',
                    'continuous_rate',
                    'start_datetime',
                    'end_datetime'
                ]
            ]);
    }

    /**
     * Successfully unpark vehicle with multiple continuous rate.
     *
     * @return void
     */
    public function test_success_small_vehicle_multiple_continuous_response()
    {
        //Generate test data
        $parkingLot = ParkingLot::factory()->create();
        $parkingSlot = ParkingSlot::factory()->create([
            'parking_lot_id' => $parkingLot->id,
            'type'           => ParkingSlotType::SMALL
        ]);

        $previousParking1 = ParkingHistory::factory()->create([
            'parking_lot_id'  => $parkingLot->id,
            'parking_slot_id' => $parkingSlot->id,
            'license_plate'   => 'abc123',
            'vehicle_size'    => VehicleSize::SMALL,
            'slot_type'       => ParkingSlotType::SMALL,
            'status'          => ParkingStatus::COMPLETED,
            'rate'            => 40.00,
            'total_hours'     => 1.5,
            'paid_hours'      => 3,
            'start_datetime'  => '2022-04-18 13:00:00',
            'end_datetime'    => '2022-04-18 14:30:00'
        ]);

        $previousParking2 = ParkingHistory::factory()->create([
            'parking_lot_id'     => $parkingLot->id,
            'parking_slot_id'    => $parkingSlot->id,
            'continuous_rate_id' => $previousParking1->id,
            'license_plate'      => 'abc123',
            'vehicle_size'       => VehicleSize::SMALL,
            'slot_type'          => ParkingSlotType::SMALL,
            'status'             => ParkingStatus::COMPLETED,
            'rate'               => 40.00,
            'total_hours'        => 1.5,
            'paid_hours'         => 3,
            'start_datetime'     => '2022-04-18 14:30:00',
            'end_datetime'       => '2022-04-18 16:00:00'
        ]);


        ParkingHistory::factory()->create([
            'parking_lot_id'     => $parkingLot->id,
            'parking_slot_id'    => $parkingSlot->id,
            'continuous_rate_id' => $previousParking2->id,
            'license_plate'      => 'abc123',
            'vehicle_size'       => VehicleSize::SMALL,
            'slot_type'          => ParkingSlotType::SMALL,
            'status'             => ParkingStatus::ONGOING,
            'rate'               => 0.00,
            'start_datetime'     => '2022-04-18 16:00:00'
        ]);

        $this->json('POST', route('parking.unpark', [
                'parkingLot' => $parkingLot->id
            ]), [
                'license_plate' => 'abc123',
                'end_datetime'=> '2022-04-18 19:00:00'
            ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'parking_lot_id', 
                    'parking_slot_id', 
                    'license_plate', 
                    'vehicle_size', 
                    'status',
                    'rate',
                    'continuous_rate',
                    'start_datetime',
                    'end_datetime'
                ]
            ]);
    }
}
