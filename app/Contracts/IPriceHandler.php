<?php

namespace App\Contracts;

use App\DTOs\PriceHandlerDTO;

interface IPriceHandler
{
    public function getPrice(string $code): PriceHandlerDTO;
}
