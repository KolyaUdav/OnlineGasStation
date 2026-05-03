<?php

namespace App\Services\API;

use App\Contracts\IPriceHandler;
use App\DTOs\PriceHandlerDTO;
use Illuminate\Support\Facades\Cache;
use Override;

class PriceHandlerCached implements IPriceHandler
{
    public function __construct(
        private IPriceHandler $baseHandler,
        private int $ttl = (3600 * 24)
    ) {}

    #[Override]
    public function getPrice(string $code): PriceHandlerDTO
    {
        $cacheKey = "fuel_price_{$code}";

        return Cache::remember($cacheKey, $this->ttl, function () use ($code) {
            return $this->baseHandler->getPrice($code);
        });
    }
}
