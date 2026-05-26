<?php

namespace Database\Seeders;

use App\Enums\Fuels;
use App\Enums\Roles\Entities;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

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

        $totalCountOrders = 1000000;

        $chunkLimit = 5000;

        $this->command->info('Начинаем загружать заказы...');

        $userIds = DB::table('users')->select('id')->limit(10)->pluck('id')->toArray();

        for ($io = 0; $io <= $totalCountOrders; $io++) {
            $randFuelEnum = Arr::random(Fuels::cases());

            $fuelCode = $randFuelEnum->value;
            $fuelName = $randFuelEnum->getLabel();
            $costInTime = fake()->randomFloat(2, 1, 5);
            $quantity = rand(10, 300);
            $cost = round($costInTime * $quantity, 2);
            $userId = $userIds[array_rand($userIds)];
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
                $ordersData = [];

                $this->command->info('Пачка заказов загружена. Осталось пачек: ' . (($totalCountOrders - $io) / $chunkLimit));
            }
        }

        $this->command->info('Заказы загружены!');
    }
}
