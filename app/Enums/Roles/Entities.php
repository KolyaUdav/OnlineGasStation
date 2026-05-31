<?php

namespace App\Enums\Roles;

use App\Services\Roles\UserRole;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Entities: int implements HasLabel, HasColor
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

    public function getColor(): string
    {
        return match ($this) {
            self::Client => 'success',
            self::Admin => 'danger',
            self::Guest => 'warning',
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
