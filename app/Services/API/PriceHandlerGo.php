<?php

namespace App\Services\API;

use App\Contracts\IPriceHandler;
use App\DTOs\PriceHandlerDTO;
use App\Traits\GoServiceUrlHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PriceHandlerGo implements IPriceHandler
{
    use GoServiceUrlHelper;

    const CODE_PARAM = 'fuel_code';

    const API_H_ACCEPT = 'application/json';

    private string $url;

    public function __construct()
    {
        $this->url = $this->buildServiceUrl('prices', 'get_gas_prices');
    }

    public function getPrice(string $code): PriceHandlerDTO
    {
        $data = $this->sendRequest($code);

        return PriceHandlerDTO::fromArray($data);
    }

    private function sendRequest(string $code): array
    {
        $response = Http::acceptJson()
            ->withoutVerifying()
            ->withHeaders([
                'Accept' => self::API_H_ACCEPT,
            ])
            ->get($this->url, [self::CODE_PARAM => $code]);

        $data = $response->json();

        if ($response->failed()) {
            $error = $data['error'] ?? 'Ошибка получения актуальной цены';

            Log::channel('go')->error($this->url . ': ' . $error . ' ' . $response->status() . '. Код: ' . $code);

            throw new \Exception($error, $response->status());
        }

        if (empty($data)) {
            Log::channel('go')->error($this->url . ': ' . 'Сервис цен вернул пустой или некорректный JSON' . ' ' . 502);

            throw new \Exception('Сервис цен вернул пустой или некорректный JSON', 502);
        }

        return (array)$data;
    }
}
