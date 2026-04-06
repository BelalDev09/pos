<?php

namespace App\Http\Resources\API\V1\App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantSeatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = is_array($this->resource) ? (object) $this->resource : $this->resource;

        return [
            'id'          => $data->id,
            'seat_number' => $data->seat_number,
            'label'       => $data->label,
            'is_occupied' => $data->is_occupied,
            'is_reserved' => $data->is_reserved ?? false,
            'guest_name'  => $data->guest_name,
            'status'      => $data->status,
        ];
    }
}
