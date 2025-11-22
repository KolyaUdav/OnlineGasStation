<?php

namespace App\Services\Roles;

class GuestRole extends UserRole
{
    public function canWatchPromotions(): bool
    {
        return true;
    }
}
