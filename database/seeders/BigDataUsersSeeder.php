<?php

namespace Database\Seeders;

use App\Enums\Fuels;
use App\Enums\Roles\Entities;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class BigDataUsersSeeder extends Seeder
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

        $totalCountUsers = 200000;

        $chunkLimit = 5000;

        $this->command->info('Загружаем пользователей...');

        $pswd = Hash::make('qwerty123');

        for ($iu = 0; $iu <= $totalCountUsers; $iu++) {
            $nameRand = Str::random(8) . " " . Str::random(12);
            $emailRand = Str::random(12) . "@example.com";

            $userData[] = [
                'name' => "$nameRand - $iu",
                'email' => "em$iu-$emailRand",
                'password' => $pswd,
                'created_at' => now(),
                'role_id' => Arr::random(Entities::cases())->value,
            ];

            if ($iu % $chunkLimit === 0) {
                DB::table('users')->insert($userData);
                unset($userData);
                $userData = [];

                $this->command->info('Пачка пользователей загружена. Осталось пачек: ' . ceil(($totalCountUsers - $iu) / $chunkLimit));
            }
        }

        $this->command->info('Пользователи загружены!');
        $this->command->info('Загружаем балансы');

        DB::table('users')->select('id')->orderBy('id')->chunk($chunkLimit, function (Collection $ids) use ($chunkLimit) {
            $userIds = $ids->pluck('id')->toArray();

            foreach ($userIds as $key => $id) {
                $amount = round(fake()->randomFloat(2, 10, 500), 2);
                
                $balanceData[] = [
                    'amount' => $amount,
                    'user_id' => $id,
                ];

                if (($key + 1) % $chunkLimit === 0) {
                    DB::table('balances')->insert($balanceData);
                    unset($balanceData);
                    $balanceData = [];
                }
            }

            unset($userIds);
        });        

         $this->command->info('Балансы загружены!');
    }
}
