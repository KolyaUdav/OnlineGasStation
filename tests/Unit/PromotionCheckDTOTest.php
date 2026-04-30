<?php

namespace Tests\Unit;

use App\DTOs\PromotionCheckDTO;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class PromotionCheckDTOTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
        $this->user->id = 123;
    }

    public function test_it_can_be_inst_from_order_data(): void
    {
        $validated = [
            'quantity' => 12,
            'fuel_type' => 'ai-92',
        ];

        $price = 100;

        $dto = PromotionCheckDTO::fromOrderData($this->user, $price, $validated);

        $this->assertEquals(123, $dto->userId);
        $this->assertEquals(12, $dto->quantity);
        $this->assertEquals('ai-92', $dto->fuelType);
        $this->assertEquals(1200, $dto->sum);
        $this->assertInstanceOf(\DateTimeInterface::class, $dto->createdAt);
    }

    public function test_it_converts_to_array_correctly(): void
    {
        $dto = new PromotionCheckDTO(
            userId: $this->user->id,
            quantity: 10,
            sum: 100,
            fuelType: 'ai-95',
            createdAt: new \DateTimeImmutable('2024-01-01 12:00:00'),
        );

        $dtoArr = $dto->toArray();

        $expected = [
            'user_id' => 123,
            'quantity' => 10,
            'sum' => 100,
            'fuel_type' => 'ai-95',
            'created_at' => '2024-01-01 12:00:00',
        ];

        $this->assertEquals($expected, $dtoArr);
    }

    public function test_it_throws_exception_if_quantity_is_negative(): void
    {
        $price = 12;

        $validated = [
            'quantity' => -5,
            'fuel_type' => 'ai-95',
        ];

        $this->expectException(\InvalidArgumentException::class);

        PromotionCheckDTO::fromOrderData($this->user, $price, $validated);
    }
}
