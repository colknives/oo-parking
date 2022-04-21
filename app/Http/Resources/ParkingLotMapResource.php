<?php

namespace App\Http\Resources;

use App\Enums\ParkingSlotType;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkingLotMapResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'parking_slot_id' => $this->id,
            'name'            => $this->name,
            'distance'        => $this->distance,
            'status'          => $this->status,
            'type'            => ParkingSlotType::$typeNames[$this->type],
            'ongoing_parking' => new ParkingHistoryResource($this->ongoingParking->first())
        ];
    }
}
