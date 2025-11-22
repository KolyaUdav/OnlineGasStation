<?php

namespace App\Services\Roles;

class AdminRole extends UserRole
{
    public function canWatchAllOrders(): bool
    {
        return true;
    }

    public function canCreatePromotion(): bool
    {
        return true;
    }

    public function canWatchPromotions(): bool
    {
        return true;
    }
}
