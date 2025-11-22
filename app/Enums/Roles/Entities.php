<?php

namespace App\Enums\Roles;

use App\Services\Roles\UserRole;

enum Entities: int
{
    case Client = 1;
    case Admin = 2;
    case Guest = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::Client => 'Клиент',
            self::Admin => 'Администратор',
            self::Guest => 'Гость',
            default => '',
        };
    }

    /**
     * Регистрирует обработчики ролей
     * @return UserRole - обработчик роли
     */
    public function getHandlerInstance(): UserRole
    {
        return match ($this) {
            self::Admin => new \App\Services\Roles\AdminRole(),
            self::Client => new \App\Services\Roles\ClientRole(),
            self::Guest => new \App\Services\Roles\GuestRole(),
            default => '',
        };
    }
}
