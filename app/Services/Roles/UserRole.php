<?php

namespace App\Services\Roles;

use App\Enums\Roles\Permissions;

class UserRole
{
    public function canWatchLastOrder(): bool
    {
        return false;
    }

    public function canWatchAllOrders(): bool
    {
        return false;
    }

    public function canCreateOrder(): bool
    {
        return false;
    }

    public function canCreatePromotion(): bool
    {
        return false;
    }

    public function canWatchPromotions(): bool
    {
        return false;
    }

    /**
     * Регистрирует логику определения наличия прав для каждого пермишна
     */
    public function hasPermission(Permissions $permission): bool
    {
        return match ($permission) {
            Permissions::WatchLastOrder => $this->canWatchLastOrder(),
            Permissions::WatchAllOrders => $this->canWatchAllOrders(),
            Permissions::CreateOrder => $this->canCreateOrder(),
            Permissions::CreatePromotion => $this->canCreatePromotion(),
            Permissions::WatchPromotions => $this->canWatchPromotions(),
            default => false,
        };
    }
}
