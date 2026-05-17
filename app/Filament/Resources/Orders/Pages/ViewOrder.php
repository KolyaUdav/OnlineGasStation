<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected ?string $heading = 'Просмотр заказа';

    protected function getHeaderActions(): array
    {
        return [
            //EditAction::make(),
        ];
    }
}
