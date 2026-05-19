<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Cache;

class OrdersChart extends ChartWidget
{
    protected ?string $heading = 'Статистика заказов';

    protected function getData(): array
    {
        $data = Cache::remember('admin_orders_chart', 3600, function () {
            return Trend::model(Order::class)
                ->between(start: now()->startOfYear(), end: now()->endOfYear())
                ->perMonth()
                ->count();
        });

        return [
            'datasets' => [
                [
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'label' => 'Количество заказов по месяцам',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
