<?php

namespace App\DTOs;

use App\Models\Order;

readonly class OrderDTO
{
    public function __construct(
        public string $fuelType,
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
}
