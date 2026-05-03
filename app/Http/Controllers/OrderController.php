<?php

namespace App\Http\Controllers;

use App\Contracts\IPriceHandler;
use App\Contracts\IPromotionsHandler;
use App\DTOs\OrderDTO;
use App\DTOs\PromotionCheckDTO;
use App\Events\OrderPlaced;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderCheckResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    const DEFAULT_MESSAGE_ERROR = 'Order error';
    const DEFAULT_MESSAGE_SUCCESS = 'Order success';

    const CONF_MSG_KEY_SUCCESS = 'order';
    const CONF_MSG_KEY_ERROR = 'order';

    protected $model = Order::class;

    public function create(
        OrderRequest $request,
        IPriceHandler $priceHandler,
        IPromotionsHandler $promotionsHandler,
    ): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $priceData = $priceHandler->getPrice($validated[Order::FIELD_FUEL_TYPE]);
        $price = $priceData->price;

        $pcDTO = PromotionCheckDTO::fromOrderData($user, $price, $validated);
        $salePercent = $promotionsHandler->getSale($pcDTO);

        $orderDTO = OrderDTO::make($validated, $price, $salePercent);
        $createdOrder = Order::createByTransaction($user, $orderDTO);

        if (!$createdOrder) {
            $this->error($this->getErrorMessage('not_create'));
        }

        event(new OrderPlaced($createdOrder));

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

    public function getCheck(Order $order): JsonResponse
    {
        return $this->success(['data' => new OrderCheckResource($order)]);
    }
}
