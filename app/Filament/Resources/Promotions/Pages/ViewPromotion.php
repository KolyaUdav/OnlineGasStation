<?php

namespace App\Filament\Resources\Promotions\Pages;

use App\Filament\Resources\Promotions\PromotionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPromotion extends ViewRecord
{
    protected static string $resource = PromotionResource::class;

    protected ?string $heading = 'Просмотр акции';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
