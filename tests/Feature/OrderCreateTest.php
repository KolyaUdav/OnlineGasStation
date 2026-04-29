<?php

namespace Tests\Feature;

use App\Models\Balance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class OrderCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_order_with_external_price(): void
    {
        $user = User::factory()->create([
            User::FIELD_ROLE_ID => 1,
        ]);

        Balance::create([
            Balance::FIELD_USER_ID => $user->id,
            Balance::FIELD_AMOUNT => 200,
        ]);

        Http::fake([
            '*/get-gas-prices*' => Http::response(['fuelCode' => 'pba', 'price' => 1.35], 200),
            '*/check-promotions*' => Http::response(['max_sale' => 0], 200), 
        ]);

        $payload = [
            'fuel_type' => 'pba',
            'quantity' => 12,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.cost', 16.2)
            ->assertJsonPath('data.cost_in_time', 1.35)
            ->assertJsonPath('data.fuel_type', 'pba');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'cost' => 16.2,
            'fuel_type' => 'pba',
        ]);

        Http::assertSent(fn ($request) => str_contains($request->url(), '/get-gas-prices'));
        Http::assertSent(fn ($request) => str_contains($request->url(), '/check-promotions'));
    }

    public function test_guest_cannot_create_order(): void
    {
        $response = $this->postJson('/api/orders', []);

        $response->assertStatus(401);
    }
}
