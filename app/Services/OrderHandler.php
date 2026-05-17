<?php

namespace App\Services;

use App\Contracts\IPriceHandler;
use App\Contracts\IPromotionsHandler;
use App\DTOs\OrderDTO;
use App\DTOs\PromotionCheckDTO;
use App\Models\Order;
use App\Models\User;

class OrderHandler
{
    public function __construct(
        private array $data,
    ) {}

    public function create(User $user): Order
    {
        $priceData = app(IPriceHandler::class)->getPrice($this->data[Order::FIELD_FUEL_TYPE]);
        $price = $priceData->price;

        $pcDTO = PromotionCheckDTO::fromOrderData($user, $price, $this->data);
        $salePercent = app(IPromotionsHandler::class)->getSale($pcDTO);

        $orderDTO = OrderDTO::make($this->data, $price, $salePercent);

        return Order::createByTransaction($user, $orderDTO);
    }
}
