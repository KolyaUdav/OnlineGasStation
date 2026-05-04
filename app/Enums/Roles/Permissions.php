<?php

namespace App\Enums\Roles;

enum Permissions: int
{
    case WatchLastOrder = 1;
    case WatchAllOrders = 2;
    case CreateOrder = 3;
    case CreatePromotion = 4;
    case WatchPromotions = 5;
    case WatchOrderCheck = 6;
    case CreateReport = 7;

    public function getDescription(): string
    {
        return match ($this) {
            self::WatchLastOrder => 'Просмотр последнего заказа',
            self::WatchAllOrders => 'Просмотр всех заказов',
            self::CreateOrder => 'Создание заказа',
            self::CreatePromotion => 'Создание акции',
            self::WatchPromotions => 'Просмотр акций',
            self::WatchOrderCheck => 'Просмотр чека',
            self::CreateReport => 'Создание отчета',
            default => '',
        };
    }
}
