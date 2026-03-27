<?php

namespace App\Contracts;

interface IPriceHandler
{
    public function getPrice(string $code): float;
}
