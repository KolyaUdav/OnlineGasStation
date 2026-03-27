<?php

namespace App\Services\API;

use App\Contracts\IPriceHandler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PriceHandlerGo implements IPriceHandler
{
    const CODE_PARAM = 'fuel_code';

    const API_H_ACCEPT = 'application/json';

    private string $url;

    public function __construct()
    {
        $this->url = Str::finish(config('app.go_url'), '/') . config('go.route_prices');
    }

    public function getPrice(string $code): float
    {
        $data = $this->sendRequest($code);

        $price = $data['price'];

        if (!isset($data['price'])) {
            throw new \Exception('Некорректный ответ от сервиса цен', 502);
        }

        return (float)$price;
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
