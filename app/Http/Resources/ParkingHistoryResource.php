<?php

namespace App\Http\Resources;

use App\Enums\VehicleSize;
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
            'id'                   => $this->id,
            'parking_lot_id'       => $this->parking_lot_id,
            'parking_slot_id'      => $this->parking_slot_id,
            'license_plate'        => $this->license_plate,
            'vehicle_size'         => $this->vehicle_size,
            'vehicle_size_name'    => VehicleSize::$typeNames[$this->vehicle_size],
            'status'               => $this->status,
            'rate'                 => $this->rate,
            'total_hours'          => $this->total_hours,
            'paid_hours'           => $this->paid_hours,
            'continuous_rate'      => $this->continuous_rate,
            'start_datetime'       => $this->start_datetime,
            'end_datetime'         => $this->end_datetime,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at
        ];
    }
}
