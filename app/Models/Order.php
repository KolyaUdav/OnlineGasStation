<?php

namespace App\Models;

use App\Enums\Fuels;
use App\Events\OrderCreated;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class Order extends BaseModel
{
    const FIELD_FUEL_NAME = 'fuel_name';
    const FIELD_FUEL_TYPE = 'fuel_type';
    const FIELD_QUANTITY = 'quantity';
    const FIELD_COST_IN_TIME = 'cost_in_time';
    const FIELD_COST = 'cost';
    const FIELD_USER_ID = 'user_id';
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';

    const FIELD_USER = 'user';

    protected $guarded = ['id'];

    /**
     * Запустит транзакцию для сохранения данных заказа и, если сохранен, запустит событие на списание с баланса
     */
    public static function createByTransaction(User $user, array $data): self
    {
        $data = self::fillOrderData($data, $user);

        $order = DB::transaction(function () use ($user, $data) {
            $salePt = $data['sale_percent'] ?? 0;
            $cost = $data[self::FIELD_COST];
            $cost = $cost - ($cost * $salePt / 100);
            $data[self::FIELD_COST] = $cost;

            if (!self::isEnoughBalance($user, $cost)) {
                throw ValidationException::withMessages([
                    'balance' => ['Недостаточно средств на балансе'],
                ]);
            }

            $order = static::apiAdd($data);

            DB::afterCommit(fn () => event(new OrderCreated($order)));

            return $order;
        });

        return $order;
    }

    /**
     * Заполнит основные данные заказа
     */
    public static function fillOrderData(array $data, User $user): array
    {
        $quantity = $data[self::FIELD_QUANTITY];
        $costInTime = $data[self::FIELD_COST_IN_TIME];

        $cost = $costInTime * $quantity;

        $data[self::FIELD_COST] = $cost;
        $data[self::FIELD_FUEL_NAME] = Fuels::from($data[self::FIELD_FUEL_TYPE])->getName();
        $data[self::FIELD_USER_ID] = $user->id;

        return $data;
    }

    /**
     * Проверит, хватает ли денег на балансе
     */
    public static function isEnoughBalance(User $user, float $cost): bool
    {
        $balance = $user->{User::FIELD_BALANCE};
        $balanceAmount = $balance->{Balance::FIELD_AMOUNT};

        if ($balanceAmount < $cost) {
            return false;
        }

        return true;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::FIELD_USER_ID);
    }
}
