<?php

namespace App\Services\API;

use App\Contracts\IPromotionsHandler;
use App\DTOs\PromotionCheckDTO;
use App\Traits\GoServiceUrlHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PromotionsHandlerGo implements IPromotionsHandler
{
    use GoServiceUrlHelper;

    public function getSale(PromotionCheckDTO $pcDTO): int
    {
        $data = $pcDTO->toArray();
        $response = $this->sendRequest($data);

        if (empty($response)) {
            Log::channel('go')->error('Пустой ответ сервиса акций', ['dto' => $pcDTO->toArray()]);

            return 0;
        }

        if (!isset($response['max_sale'])) {
            Log::channel('go')->error('Ожидаемый параметр ответа от сервиса акций max_sale не был получен');

            throw new \RuntimeException('Ожидаемый параметр ответа от сервиса акций max_sale не был получен', 502);
        }

        return (int)$response['max_sale'];
    }

    private function sendRequest(array $data): array
    {
        try {
            return Http::withoutVerifying()
                ->acceptJson()
                ->get($this->buildServiceUrl('promotions', 'check_promotions'), $data)
                ->throw()
                ->json() ?? [];
        }  catch (\Exception $e) {
            Log::channel('go')->error("Сервис акций недоступен: {$e->getMessage()}");
            return [];
        }
    }
}
