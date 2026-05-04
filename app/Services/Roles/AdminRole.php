<?php

namespace App\Services\Roles;

use Override;

class AdminRole extends UserRole
{
    #[Override]
    public function canWatchAllOrders(): bool
    {
        return true;
    }

    #[Override]
    public function canCreatePromotion(): bool
    {
        return true;
    }

    #[Override]
    public function canWatchPromotions(): bool
    {
        return true;
    }

    #[Override]
    public function canCreateReport(): bool
    {
        return true;
    }
}
