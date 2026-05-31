<?php

namespace Database\Seeders;

use App\Enums\Fuels;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class BigDataOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::connection()->disableQueryLog();

        if (class_exists(\Laravel\Telescope\Telescope::class)) {
            \Laravel\Telescope\Telescope::stopRecording();
        }

        \Illuminate\Support\Facades\DB::connection()->unsetEventDispatcher();

        $totalCountOrders = 2000000;

        $chunkLimit = 5000;

        $this->command->info('Начинаем загружать заказы...');

        $userIds = [];

        DB::table('users')->select('id')->orderBy('id')->chunk($chunkLimit, function (Collection $ids) use ($chunkLimit, &$userIds) {
            $userIds = array_merge($userIds, $ids->pluck('id')->toArray());
        });

        for ($io = 0; $io <= $totalCountOrders; $io++) {
            $randFuelEnum = Arr::random(Fuels::cases());

            $fuelCode = $randFuelEnum->value;
            $fuelName = $randFuelEnum->getLabel();
            $costInTime = fake()->randomFloat(2, 1, 5);
            $quantity = rand(10, 300);
            $cost = round($costInTime * $quantity, 2);
            $userId = $userIds[mt_rand(0, count($userIds) - 1)];
            $createdAt = now()->subDays(rand(0, 180))->subMinutes(rand(0, 1440));
            $checkPath = "http://localhost/" . Str::random(32) . ".pdf";

            $ordersData[] = [
                'quantity' => $quantity,
                'fuel_type' => $fuelCode,
                'fuel_name' => $fuelName,
                'cost_in_time' => $costInTime,
                'cost' => $cost,
                'user_id' => $userId,
                'created_at' => $createdAt,
                'check_path' => $checkPath,
            ];

            if ($io % $chunkLimit === 0) {
                DB::table('orders')->insert($ordersData);
                unset($ordersData);
                $ordersData = [];

                $this->command->info('Пачка заказов загружена. Осталось пачек: ' . (($totalCountOrders - $io) / $chunkLimit));
            }
        }

        $this->command->info('Заказы загружены!');
    }
}
