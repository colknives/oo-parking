<?php

namespace Database\Factories;

use App\Models\ParkingSlot;
use App\Models\ParkingLot;
use App\Enums\ParkingSlotType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParkingSlotFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ParkingSlot::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'parking_lot_id' => function () {
                return ParkingLot::factory()->create()->id;
            },
            'name'           => $this->faker->word,
            'distance'       => $this->faker->randomElements([0,1,2,3,4,5,6,7], 8),
            'type'           => $this->faker->randomElement(ParkingSlotType::$types)
        ];
    }
}
