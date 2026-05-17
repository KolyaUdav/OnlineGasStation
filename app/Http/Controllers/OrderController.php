<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderCheckResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderHandler;
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
    ): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $createdOrder = (new OrderHandler($validated))->create($user);

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
