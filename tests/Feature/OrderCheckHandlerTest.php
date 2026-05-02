<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\API\OrderCheckHandlerNode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderCheckHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_and_saves_pdf_successfully(): void
    {
        Storage::fake('public');

        $endpoint = config('node.pdf_generator.endpoint');
        $fakePdfContent = "%PDF-1.4 fake content";

        Http::fake([
            $endpoint => Http::response($fakePdfContent, 200),
        ]);

        $user = User::factory()->create();

        $order = Order::create([
            Order::FIELD_COST => 20,
            Order::FIELD_COST_IN_TIME => 1,
            Order::FIELD_FUEL_NAME => 'АИ-95',
            Order::FIELD_FUEL_TYPE => 'ai-95',
            Order::FIELD_QUANTITY => 20,
            Order::FIELD_USER_ID => $user->id,
        ]);

        $handler = new OrderCheckHandlerNode();
        $handler->generate($order);

        Http::assertSent(function ($request) use ($endpoint, $order) {
            return $request->url() === $endpoint &&
                $request->method() === 'POST' &&
                isset($request['html']) &&
                str_contains($request['html'], (string)$order->id);
        });

        $expectedPath = "order_checks/order_{$order->id}.pdf";
        Storage::disk('public')->assertExists($expectedPath);
        $this->assertEquals($fakePdfContent, Storage::disk('public')->get($expectedPath));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            Order::FIELD_CHECK_PATH => $expectedPath,
        ]);
    }

    public function test_it_handles_service_error_gracefully(): void
    {
        Storage::fake('public');
        Http::fake([
            '*' => Http::response('Service Unavailable', 503)
        ]);

        $user = User::factory()->create();

        $order = Order::create([
            Order::FIELD_COST => 20,
            Order::FIELD_COST_IN_TIME => 1,
            Order::FIELD_FUEL_NAME => 'АИ-95',
            Order::FIELD_FUEL_TYPE => 'ai-95',
            Order::FIELD_QUANTITY => 20,
            Order::FIELD_USER_ID => $user->id,
        ]);

        $handler = new OrderCheckHandlerNode();
        $handler->generate($order);

        // Файл не должен быть создан
        Storage::disk('public')->assertDirectoryEmpty('order_checks');
        
        // Путь в базе должен остаться пустым (null)
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            Order::FIELD_CHECK_PATH => null,
        ]);
    }
}
