<?php

namespace Database\Factories;

use App\Models\ParkingLot;
use App\Enums\ParkingSlotType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParkingLotFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ParkingLot::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $slots = [
            [
                'distance' => [1,2,3],
                'size'     => ParkingSlotType::LARGE
            ],
            [
                'distance' => [2,1,2],
                'size'     => ParkingSlotType::LARGE
            ],
            [
                'distance' => [3,2,1],
                'size'     => ParkingSlotType::LARGE
            ]
        ];

        return [
            'name'       => $this->faker->word,
            'total_slot' => count($slots),
            'slots'      => json_encode($slots)
        ];
    }
}
