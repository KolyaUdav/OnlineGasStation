<?php

namespace App\Services\Admin;

use App\Contracts\IOrderAnalyzer;
use App\DTOs\ReportDTO;
use App\Enums\Fuels;
use App\Enums\Reports\Types;
use Illuminate\Support\Facades\DB;
use Override;

class OrderAnalyzer implements IOrderAnalyzer
{
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    #[Override]
    public function getResult(ReportDTO $dto): array
    {
        $result = [];

        $from = $dto->from->format(self::DATE_TIME_FORMAT);
        $to = $dto->to->format(self::DATE_TIME_FORMAT);
        $type = $dto->type->value;

        if ($type === Types::GeneralAnalytics->value) {
            $result = $this->getGeneralAnalytics($from, $to);
        }

        return $result;
    }

    protected function getGeneralAnalytics(string $from, string $to): array
    {
        $codes = array_column(Fuels::cases(), 'value');

        $ordersQuery = DB::table('orders')
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to);

        $maxPrice = (clone $ordersQuery)->max('cost');
        $maxQuantity = (clone $ordersQuery)->max('quantity');
        $orders = (clone $ordersQuery)
            ->select(['cost', 'quantity'])
            ->orderBy('id')
            ->lazy();

        $maxAttrByTypes = DB::table('orders')
            ->select('fuel_type')
            ->selectRaw('MAX(cost) as max_cost')
            ->selectRaw('MAX(cost_in_time) as max_cost_in_time')
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->whereIn('fuel_type', $codes)
            ->groupBy('fuel_type')
            ->get()
            ->keyBy('fuel_type');

        $maxCostByType = $maxAttrByTypes->map->max_cost->toArray();
        $maxCostInTimeByType = $maxAttrByTypes->map->max_cost_in_time->toArray();

        $totalCost = 0;
        $totalQuantity = 0;

        foreach ($orders as $order) {
            $totalCost += $order->cost;
            $totalQuantity += $order->quantity;
        }

        return [
            'total_cost' => round($totalCost, 2),
            'total_quantity' => $totalQuantity,
            'max_price' => round($maxPrice, 2),
            'max_quantity' => $maxQuantity,
            'max_cost_by_type' => $maxCostByType,
            'max_cost_in_time_by_type' => $maxCostInTimeByType,
        ];
    }
}
