<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Enums\Reports\Statuses;
use App\Filament\Resources\Reports\ReportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportResource::class;

    protected ?string $heading = 'Детали отчета';

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getPollingInterval(): ?string
    {
        $record = $this->getRecord();

        if ($record->status === Statuses::Completed || $record->status === Statuses::Failed) {
            return null; 
        }

        return '3s'; 
    }
}
