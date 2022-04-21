<?php

namespace Database\Seeders;

use App\Enums\ParkingSlotType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ParkingLot;
use App\Models\ParkingSlot;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //Generate default data for parking lot and slot
        $parkingLot = ParkingLot::factory()->create([
            'total_entry' => 4,
            'total_slot'  => 8
        ]);

        $slots = [
            [
                'name'     => 'Slot 1',
                'distance' => ["0", "3", "4", "7"],
                'type'     => ParkingSlotType::SMALL
            ],
            [
                'name'     => 'Slot 2',
                'distance' => ["1", "2", "5", "6"],
                'type'     => ParkingSlotType::MEDIUM
            ],
            [
                'name'     => 'Slot 3',
                'distance' => ["2", "1", "6", "5"],
                'type'     => ParkingSlotType::LARGE
            ],
            [
                'name'     => 'Slot 4',
                'distance' => ["3", "0", "7", "4"],
                'type'     => ParkingSlotType::SMALL
            ],
            [
                'name'     => 'Slot 5',
                'distance' => ["4", "7", "0", "3"],
                'type'     => ParkingSlotType::MEDIUM
            ],
            [
                'name'     => 'Slot 6',
                'distance' => ["5", "6", "1", "2"],
                'type'     => ParkingSlotType::SMALL
            ],
            [
                'name'     => 'Slot 7',
                'distance' => ["6", "5", "2", "1"],
                'type'     => ParkingSlotType::LARGE
            ],
            [
                'name'     => 'Slot 8',
                'distance' => ["7", "4", "3", "0"],
                'type'     => ParkingSlotType::SMALL
            ]
        ];

        foreach($slots as $slot) {
            $parkingSlot = new ParkingSlot;
            $parkingSlot->parking_lot_id = $parkingLot->id;
            $parkingSlot->name = $slot['name'];
            $parkingSlot->distance = $slot['distance'];
            $parkingSlot->type = $slot['type'];
            $parkingSlot->save();
        }
    }
}
