<?php

namespace Database\Seeders;

use App\Enums\Roles\Entities;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $names = [
            [
                'name' => 'Valerka',
                'email' => 'valerka@example.com',
                'password' => Hash::make('Valerka1_'),
                'role_id' => Entities::Admin->value,
            ],
            [
                'name' => 'Anna Petrova',
                'email' => 'anna.petrovа@example.com',
                'password' => Hash::make('anna.petrovа@example.com'),
                'role_id' => Entities::Client->value,
            ],
            [
                'name' => 'Boris Ivanov',
                'email' => 'boris.ivanov@example.com',
                'password' => Hash::make('boris.ivanov@example.com'),
                'role_id' => Entities::Admin->value,
            ],
            [
                'name' => 'Katya Sokolova',
                'email' => 'katya.sokolova@example.com',
                'password' => Hash::make('katya.sokolova@example.com'),
                'role_id' => Entities::Client->value,
            ],
            [
                'name' => 'Igor Smirnov',
                'email' => 'igor.smirnov@example.com',
                'password' => Hash::make('igor.smirnov@example.com'),
                'role_id' => Entities::Client->value,
            ],
            [
                'name' => 'Masha Kuznetsova',
                'email' => 'masha.kuznetsova@example.com',
                'password' => Hash::make('masha.kuznetsova@example.com'),
                'role_id' => Entities::Admin->value,
            ],
            [
                'name' => 'Sergei Popov',
                'email' => 'sergei.popov@example.com',
                'password' => Hash::make('qwerty'),
                'role_id' => Entities::Client->value,
            ],
            [
                'name' => 'Lena Moroz',
                'email' => 'lena.moroz@example.com',
                'password' => Hash::make('lena.moroz@example.com'),
                'role_id' => Entities::Admin->value,
            ],
            [
                'name' => 'Dima Fedorov',
                'email' => 'dima.fedorov@example.com',
                'password' => Hash::make('dima.fedorov@example.com'),
                'role_id' => Entities::Client->value,
            ],
            [
                'name' => 'Oleg Nikitin',
                'email' => 'oleg.nikitin@example.com',
                'password' => Hash::make('oleg.nikitin@example.com'),
                'role_id' => Entities::Client->value,
            ],
            [
                'name' => 'Sasha',
                'email' => 'sasha@example.com',
                'password' => Hash::make('qwerty'),
                'role_id' => Entities::Client->value,
            ],
        ];

        foreach ($names as $userData) {
            $exists = User::where('email', $userData['email'])->exists();

            if ($exists) {
                continue; // пропускаем уже существующего пользователя
            }

            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'role_id' => $userData['role_id'],
            ]);

            Balance::create([
                'user_id' => $user->id,
                'amount' => 100.0,
            ]);
        }
    }
}
