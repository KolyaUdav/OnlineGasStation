<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\API\PriceHandler;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends BaseController
{
    const DEFAULT_MESSAGE_ERROR = 'Order error';
    const DEFAULT_MESSAGE_SUCCESS = 'Order success';

    const CONF_MSG_KEY_SUCCESS = 'order';
    const CONF_MSG_KEY_ERROR = 'order';

    protected $model = Order::class;

    public function create(OrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $prices = PriceHandler::getPrices();
        $price = PriceHandler::getPriceByCode($data[Order::FIELD_FUEL_TYPE], $prices);

        $data[Order::FIELD_COST_IN_TIME] = $price;

        $promotionsData = [
            'user_id' => $user->id,
            'quantity' => $data[Order::FIELD_QUANTITY],
            'sum' => $price * $data[Order::FIELD_QUANTITY],
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'fuel_type' => $data[Order::FIELD_FUEL_TYPE],
        ];

        $salePercent = $this->getActualSale($promotionsData);
        $data['sale_percent'] = $salePercent;

        $createdOrder = Order::createByTransaction($user, $data);

        if (!$createdOrder) {
            $this->error($this->getErrorMessage('not_create'));
        }

        return $this->success(['data' => new OrderResource($createdOrder)]);
    }

    public function getLastOrder(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->error();
        }

        $lastOrder = $user->getLastOrder();

        if (!$lastOrder) {
            return $this->error($this->getErrorMessage('last_not_found'), 404);
        }

        return $this->success(['data' => $lastOrder]);
    }

    protected function getActualSale(array $data): int
    {
        try {
            $route = config('app.go_url') . '/api/check-promotions';
            $response = Http::withoutVerifying()->acceptJson()->get($route, $data);
        } catch (\Exception $e) {
            Log::error('Ошибка соединения с сервисом акций: ' . $e->getMessage() . ', ' . $e->getCode());

            return 0;
        }

        if ($response->ok()) {
            $result = $response->body();

            if (!json_validate($result)) {
                Log::error("Ответ сервиса акций не является валидным JSON: $result");

                return 0;
            }

            $resArr = json_decode($result, true);

            if (!isset($resArr['max_sale'])) {
                Log::error("Ожидаемый параметр ответа от сервиса акций max_sale не был получен");

                return 0;
            }

            return (int)$resArr['max_sale'];
        } else {
            Log::error('Ошибка сервиса акций: ' . $response->body());
        }

        return 0;
    }
}
