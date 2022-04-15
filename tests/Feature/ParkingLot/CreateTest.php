<?php

namespace Tests\Feature\ParkingLot;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\ParkingLot;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Successfully create parking lot record.
     *
     * @return void
     */
    public function success()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
