<?php

namespace App\DTOs;

use App\Enums\Fuels;
use App\Models\Order;
use App\Models\User;

readonly class OrderDTO
{
    public function __construct(
        public Fuels $fuelType,
        public int $quantity,
        public float $costInTime,
        public int $salePercent,
    ) {}

    public static function make(array $validated, float $price, int $sale): self
    {
        return new self(
            fuelType: $validated[Order::FIELD_FUEL_TYPE],
            quantity: $validated[Order::FIELD_QUANTITY],
            costInTime: $price,
            salePercent: $sale,
        );
    }

    public function toArray(User $user): array
    {
        $data = [];

        $quantity = $this->quantity;
        $costInTime = $this->costInTime;
        $cost = $costInTime * $quantity;
        $cost = round($cost - ($cost * $this->salePercent / 100), 2); // Подсчет с учетом процента скидки

        $data['quantity'] = $quantity ?? 0;
        $data['cost_in_time'] = $costInTime ?? 0.00;
        $data['sale_percent'] = $this->salePercent ?? 0;
        $data['fuel_type'] = $this->fuelType->value;
        $data['cost'] = $cost;
        $data['fuel_name'] = $this->fuelType->getLabel();
        $data['user_id'] = $user->id;

        return $data;
    }
}
