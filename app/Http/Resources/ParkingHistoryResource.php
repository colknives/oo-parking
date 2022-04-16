<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ParkingHistoryResource extends JsonResource
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
            'parking_lot_id'  => $this->parking_lot_id,
            'parking_slot_id' => $this->parking_slot_id,
            'license_plate'   => $this->license_plate,
            'vehicle_size'    => $this->vehicle_size,
            'status'          => $this->status,
            'rate'            => $this->rate,
            'continuous_rate' => $this->continuous_rate,
            'start_datetime'  => $this->start_datetime,
            'end_datetime'    => $this->end_datetime,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at
        ];
    }
}
