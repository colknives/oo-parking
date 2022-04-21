<?php

namespace Tests\Feature\ParkingHistory;

use App\Enums\ParkingStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\ParkingLot;
use App\Models\ParkingSlot;
use App\Models\ParkingHistory;
use App\Enums\VehicleSize;
use Carbon\Carbon;

class ParkTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Successfully park vehicle.
     *
     * @return void
     */
    public function test_success_response()
    {
        //Generate test data
        $parkingLot = ParkingLot::factory()->create();
        $parkingSlots = ParkingSlot::factory(3)->create([
            'parking_lot_id' => $parkingLot->id
        ]);

        ParkingHistory::factory()->create([
            'parking_lot_id'  => $parkingLot->id,
            'parking_slot_id' => $parkingSlots[0]->id
        ]);

        $this->json('POST', route('parking.park', [
                'parkingLot' => $parkingLot->id
        ]), [
                'entry_point' => 1,
                'license_plate' => 'abc123',
                'vehicle_size' => VehicleSize::SMALL,
                // 'start_datetime' => '2022-04-20 08:00:00'
        ])
            ->assertCreated()
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
     * Successfully park vehicle with continuous rate applied.
     *
     * @return void
     */
    public function test_success_continuous_rate_response()
    {
        //Generate test data
        $parkingLot = ParkingLot::factory()->create();
        $parkingSlots = ParkingSlot::factory(3)->create([
            'parking_lot_id' => $parkingLot->id
        ]);

        //Create parking history data with legible for continuous rate
        $history = ParkingHistory::factory()->create([
            'license_plate'   => 'abc123',
            'vehicle_size'    => VehicleSize::SMALL,
            'parking_lot_id'  => $parkingLot->id,
            'parking_slot_id' => $parkingSlots[0]->id,
            'start_datetime'  => Carbon::now()->subHour(3),
            'end_datetime'    => Carbon::now()->subMinutes(15),
            'status'          => ParkingStatus::COMPLETED
        ]);

        $this->json('POST', route('parking.park', [
                'parkingLot' => $parkingLot->id
        ]), [
                'entry_point' => 2,
                'license_plate' => 'abc123',
                'vehicle_size' => VehicleSize::SMALL,
                'start_datetime' => Carbon::now()
        ])
            ->assertCreated()
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
            ])
            ->assertJsonFragment([
                'license_plate' => 'abc123',
                'continuous_rate' => true
            ]);
    }

    /**
     * Failed to park vehicle due to no vacancy.
     *
     * @return void
     */
    public function test_no_vacancy_response()
    {
        //Generate test data
        $parkingLot = ParkingLot::factory()->create();
        $parkingSlot = ParkingSlot::factory()->create([
            'parking_lot_id' => $parkingLot->id
        ]);

        ParkingHistory::factory()->create([
            'parking_lot_id'  => $parkingLot->id,
            'parking_slot_id' => $parkingSlot->id
        ]);

        $this->json('POST', route('parking.park', [
                'parkingLot' => $parkingLot->id
        ]), [
                'entry_point' => 2,
                'license_plate' => 'abc123',
                'vehicle_size' => VehicleSize::SMALL,
                'start_datetime' => Carbon::now()
        ])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'message'
            ]);
    }

    /**
     * Failed to park vehicle due to non existing entry point.
     *
     * @return void
     */
    public function test_non_existing_entry_point_response()
    {
        //Generate test data
        $parkingLot = ParkingLot::factory()->create();
        $parkingSlot = ParkingSlot::factory()->create([
            'parking_lot_id' => $parkingLot->id
        ]);

        ParkingHistory::factory()->create([
            'parking_lot_id'  => $parkingLot->id,
            'parking_slot_id' => $parkingSlot->id
        ]);

        $this->json('POST', route('parking.park', [
                'parkingLot' => $parkingLot->id
        ]), [
                'entry_point' => 100,
                'license_plate' => 'abc123',
                'vehicle_size' => VehicleSize::SMALL,
                'start_datetime' => Carbon::now()
        ])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'message'
            ]);
    }
}
