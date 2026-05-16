<?php

namespace App\Filament\Resources\Reports\Pages;

use App\DTOs\ReportDTO;
use App\Enums\Reports\Statuses;
use App\Enums\Reports\Types;
use App\Filament\Resources\Reports\ReportResource;
use App\Jobs\FillOrdersReportJob;
use App\Models\Report;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class CreateReport extends CreateRecord
{
    protected static string $resource = ReportResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('from')
                    ->required(),
                DateTimePicker::make('to')
                    ->required(),
                Select::make('type')
                    ->options(Types::class)
                    ->required(),
            ]);
    }

    public function handleRecordCreation(array $data): Report
    {
        $dto = ReportDTO::fromArray($data);

        $report = Report::create([
            'user_id' => Auth::user()?->id,
            'status' => Statuses::Pending,
            'type' => $dto->type,
        ]);

        FillOrdersReportJob::dispatch($report->id, $dto)->onQueue('high');

        return $report;
    }
}
