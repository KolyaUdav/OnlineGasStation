<?php

namespace App\Contracts;

use App\DTOs\PromotionCheckDTO;

interface IPromotionsHandler
{
    public function getSale(PromotionCheckDTO $pcDTO): int;
}
