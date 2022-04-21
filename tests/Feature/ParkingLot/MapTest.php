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

class MapTest extends TestCase
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
            'parking_slot_id' => $parkingSlots[0]->id,
            'status' => ParkingStatus::ONGOING
        ]);

        $this->json('GET', route('parking.map', [
            'parkingLot' => $parkingLot->id
        ]))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    [
                        'parking_slot_id',
                         'name',
                         'distance',
                         'status',
                         'type',
                         'ongoing_parking'
                    ]
                ]
            ]);
    }
}
