<?php

namespace App\Services;

use App\Contracts\IPriceHandler;
use App\Contracts\IPromotionsHandler;
use App\DTOs\OrderDTO;
use App\DTOs\PromotionCheckDTO;
use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderHandler
{
    public function __construct(
        private array $data,
    ) {}

    public function create(User $user): Order
    {
        $fuelType = $this->data['fuel_type'];

        if ($fuelType instanceof \App\Enums\Fuels) {
            $fuelTypeCode = $fuelType->value;
        } else {
            $fuelTypeCode = $this->data['fuel_type'];
        }

        $priceData = app(IPriceHandler::class)->getPrice($fuelTypeCode);
        $price = $priceData->price;

        $pcDTO = PromotionCheckDTO::fromOrderData($user, $price, $this->data);
        $salePercent = app(IPromotionsHandler::class)->getSale($pcDTO);

        $orderDTO = OrderDTO::make($this->data, $price, $salePercent);

        return $this->createByTransaction($user, $orderDTO);
    }

    /**
     * Запустит транзакцию для сохранения данных заказа и, если сохранен, запустит событие на списание с баланса
     */
    protected function createByTransaction(User $user, OrderDTO $dto): Order
    {
        $order = DB::transaction(function () use ($user, $dto) {
            $data = $dto->toArray($user);

            if (!$this->isEnoughBalance($user, $data['cost'])) {
                throw ValidationException::withMessages([
                    'balance' => ['Недостаточно средств на балансе'],
                ]);
            }

            $order = Order::apiAdd($data);

            DB::afterCommit(fn () => event(new OrderCreated($order)));

            return $order;
        });

        return $order;
    }

    /**
     * Проверит, хватает ли денег на балансе
     */
    protected static function isEnoughBalance(User $user, float $cost): bool
    {
        $balance = $user->balance;
        $balanceAmount = $balance->amount;

        if ($balanceAmount < $cost) {
            return false;
        }

        return true;
    }
}
