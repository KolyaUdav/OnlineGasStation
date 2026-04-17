<?php

return [
    'prices' => [
        'host' => env('GO_GAS_PRICES_URL', 'http://localhost'),
        'port' => env('GO_GAS_PRICES_PORT', '8080'),
        'endpoints' => [
            'get_gas_prices' => '/api/get-gas-prices',
        ],
    ],
    'promotions' => [
        'host' => env('GO_PROMOTIONS_URL', 'http://localhost'),
        'port' => env('GO_PROMOTIONS_PORT', '8080'),
        'endpoints' => [
            'check_promotions' => '/api/check-promotions',
        ],
    ],
];