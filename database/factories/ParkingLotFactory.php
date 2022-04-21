<?php

namespace Database\Factories;

use App\Models\ParkingLot;
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
        return [
            'name'        => $this->faker->word,
            'total_entry' => 4,
            'total_slot'  => 8,
        ];
    }
}
