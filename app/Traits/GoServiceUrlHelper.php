<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GoServiceUrlHelper
{
    /**
     * Собирает полный URL на основе конфига сервиса.
     * 
     * @param string $serviceKey Ключ сервиса в конфиге (например, 'prices' или 'promotions')
     * @param string $endpointKey Ключ эндпоинта (например, 'get_prices')
     * @return string
     */
    protected function buildServiceUrl(string $serviceKey, string $endpointKey): string
    {
        $config = config("go.{$serviceKey}");

        if (!$config) {
            throw new \InvalidArgumentException("Конфигурация для сервиса [{$serviceKey}] не найдена.");
        }

        $host = Str::finish($config['host'], '');
        $port = $config['port'] ? ":{$config['port']}" : '';
        $endpoint = Str::start($config['endpoints'][$endpointKey] ?? '', '/');

        // Результат: http://localhost:8081/api/endpoint
        return $host . $port . $endpoint;
    }
}
