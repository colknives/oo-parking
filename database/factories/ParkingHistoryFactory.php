<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\ParkingHistory;
use App\Models\ParkingLot;
use App\Enums\ParkingSlotType;
use App\Enums\ParkingStatus;
use App\Enums\HourlyRate;
use App\Models\ParkingSlot;

class ParkingHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ParkingHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $parkingLot = ParkingLot::factory()->create();

        return [
            'parking_lot_id'       => $parkingLot->id,
            'parking_slot_id'      => function () use ($parkingLot) {
                return ParkingSlot::factory()->create([
                    'parking_lot_id' => $parkingLot->id
                ])->id;
            },
            'license_plate'        => $this->faker->word,
            'status'               => ParkingStatus::ONGOING,
            'rate'                 => HourlyRate::SMALL,
            'start_datetime'       => Carbon::now()
        ];
    }
}
