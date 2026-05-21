<?php

namespace App\DTOs;

use App\Enums\Fuels;
use App\Models\User;

readonly class PromotionCheckDTO
{
    public function __construct(
        public int $userId,
        public int $quantity,
        public float $sum,
        public Fuels $fuelType,
        public \DateTimeInterface $createdAt,
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'quantity' => $this->quantity,
            'sum' => $this->sum,
            'fuel_type' => $this->fuelType->value,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }

    public static function fromOrderData(User $user, float $price, array $validated): self
    {
        $quantity = (int)$validated['quantity'];

        if ($quantity <= 0) {
            throw new \InvalidArgumentException('количество должно быть позитивным числом');
        }

        return new self(
            userId: $user->id,
            quantity: $quantity,
            sum: $price * $quantity,
            fuelType: $validated['fuel_type'],
            createdAt: now(),
        );
    }
}
