<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            Order::FIELD_FUEL_TYPE => $this->{Order::FIELD_FUEL_TYPE},
            Order::FIELD_QUANTITY => $this->{Order::FIELD_QUANTITY},
            Order::FIELD_COST_IN_TIME => $this->{Order::FIELD_COST_IN_TIME},
            Order::FIELD_COST => $this->{Order::FIELD_COST},
            Order::FIELD_USER_ID => $this->{Order::FIELD_USER_ID},
            Order::FIELD_CREATED_AT => $this->{Order::FIELD_CREATED_AT},
            Order::FIELD_UPDATED_AT => $this->{Order::FIELD_UPDATED_AT},
        ];
    }
}
