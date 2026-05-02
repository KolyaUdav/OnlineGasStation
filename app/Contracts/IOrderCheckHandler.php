<?php

namespace App\Contracts;

use App\Models\Order;

interface IOrderCheckHandler
{
    public function generate(Order $order): void;
}
