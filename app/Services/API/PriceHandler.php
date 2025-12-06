<?php

namespace App\Services\API;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

/**
 * Класс получения и обработки актуальных цен на топливо
 */
class PriceHandler
{
    const API_H_ACCEPT = 'application/json';

    const BX_PARAMS_MAP = [
        'CODE' => Order::FIELD_FUEL_TYPE,
        'PRICE' => Order::FIELD_COST_IN_TIME,
    ];

    /**
     * Получит весь перечень цен на топливо
     */
    public static function getPrices(): array
    {
        $result = self::getMockResponse();
        $mappedResult = self::map($result);

        return $mappedResult;
    }

    /**
     * Получит цену на конкретное топливо по коду из \App\Enums\Fuels
     */
    public static function getPriceByCode(string $code, array $mappedItems): float
    {
        $item = array_filter($mappedItems, fn ($mItem) => $mItem[Order::FIELD_FUEL_TYPE] === $code);
        $item = array_shift($item);

        if (!$item) {
            throw new \Exception(config('errors.api_price.el_code_not_exists', 'Элемент с указанным кодом отсутствует'));
        }

        return (float)$item[Order::FIELD_COST_IN_TIME];
    }

    private static function map(array $bxResult): array
    {
        return array_map(function ($bxItem) {
            return [
                self::BX_PARAMS_MAP['CODE'] => $bxItem['CODE'],
                self::BX_PARAMS_MAP['PRICE'] => $bxItem['PRICE'],
            ];
        }, $bxResult);
    }

    private static function getMockResponse(): array
    {
        return [
            [
                'CODE' => 'ai-95',
                'PRICE' => 10,
            ],
            [
                'CODE' => 'ai-92',
                'PRICE' => 8,
            ],
            [
                'CODE' => 'ai-98',
                'PRICE' => 15,
            ],
            [
                'CODE' => 'dt-euro',
                'PRICE' => 5.6,
            ],
            [
                'CODE' => 'dt-arctic',
                'PRICE' => 18.3,
            ],
            [
                'CODE' => 'adblue',
                'PRICE' => 14,
            ],
            [
                'CODE' => 'gas',
                'PRICE' => 4.6,
            ],
        ];
    }

    private static function sendRequest(): array
    {
        $response = Http::acceptJson()
            ->withoutVerifying()
            ->withHeaders([
                'Accept' => self::API_H_ACCEPT,
                'BX-token' => config('price.token'),
            ])
            ->get(config('price.url'));
        
        if ($response->ok()) {
            $result = @json_decode($response->body(), true);

            if (isset($result['status']) && $result['status'] === 'error') {
                throw new \Exception(config('errors.api_price.invalid_auth', 'Не удалось авторизоваться'));
            }

            if (isset($result['status']) && $result['status'] === 'success' && isset($result['data'])) {
                return $result['data'];
            }
        } else {
            throw new \Exception(config('errors.api_price.bad_request', 'Не удалось получить доступ к API'));
        }

        return [];
    }
}
