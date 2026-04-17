<?php

namespace App\DTOs;

readonly class PriceHandlerDTO
{
    public function __construct(
        public string $fuelCode,
        public float $price,
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['price'])) {
            throw new \InvalidArgumentException('Некорректный ответ от сервиса цен', 502);
        }

        return new self(
            $data['fuelCode'] ?? '',
            (float)$data['price'],
        );
    }
}
