<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Enums\VehicleSize;

class ParkVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'entry_point' => ['required', 'integer'],
            'license_plate' => ['required'],
            'vehicle_size' => ['required', Rule::in(VehicleSize::$sizes)],
            'start_datetime' => ['required', 'date']
        ];
    }
}
