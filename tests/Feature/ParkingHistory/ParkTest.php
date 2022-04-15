<?php

namespace Tests\Feature\ParkingHistory;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\ParkingLot;
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
        $parkingLot = ParkingLot::factory()->create();

        dd($parkingLot);

        $response = $this->json('POST', route('parking.park', [
                'parkingLot' => $parkingLot->id
        ]), [
                'entry_point' => 1,
                'license_plate' => 'abc123',
                'size' => VehicleSize::SMALL,
                'start_datetime' => Carbon::now()
        ]);

        dd($response->getContent());

        //     ->assertOk()
        //     ->assertJsonStructure([
        //         'data' => [
        //             ['id', 'name', 'address', 'description', 'distance'],
        //         ],
        //         'meta' => ['current_page', 'from', 'last_page', 'per_page', 'to', 'total'],
        //     ]);

        // dd($parkingLot);

        // $response = $this->get('/');

        // $response->assertStatus(200);
    }
}
