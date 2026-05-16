<?php

namespace App\Filament\Resources\Reports;

use App\Enums\Reports\Statuses;
use App\Filament\Resources\Reports\Pages\CreateReport;
use App\Filament\Resources\Reports\Pages\ListReports;
use App\Filament\Resources\Reports\Pages\ViewReport;
use App\Filament\Resources\Reports\Tables\ReportsTable;
use App\Livewire\OrdersReportResult;
use App\Models\Report;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Отчеты по заказам';

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Общие данные')
                ->schema([
                    TextEntry::make('status')
                        ->label('Статус')
                        ->badge(),
                    TextEntry::make('type')
                        ->label('Тип')
                        ->badge(),
                ])
                ->columnSpanFull()
                ->columns(2),
            Livewire::make(OrdersReportResult::class, fn ($record) => ['record' => $record])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return ReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateReport::route('/create'),
            'index' => ListReports::route('/'),
            'view' => ViewReport::route('/{record}'),
        ];
    }
}
