<?php

namespace App\Filament\Resources\Reports\Tables;

use App\Enums\Reports\Statuses;
use App\Enums\Reports\Types;
use App\Models\Report;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Report::query()
                    ->with(['user'])
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Создатель')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->label('Тип')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->label('Статус')
                    ->searchable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->label('Сформирован')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('completed_at')
                    ->schema([
                        DateTimePicker::make('completed_at_start')
                            ->label('Дата создания с'),
                        DateTimePicker::make('completed_at_finish')
                            ->label('Дата создания по'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['completed_at_start'], 
                            fn (Builder $query, $date) => $query->whereDate('completed_at', '>=', $date)
                        )
                        ->when(
                            $data['completed_at_finish'],
                            fn (Builder $query, $date) => $query->whereDate('completed_at', '<=', $date)
                        );
                    }),
                SelectFilter::make('status')
                    ->label('Статус')
                    ->placeholder('Все статусы')
                    ->options(Statuses::class),
                SelectFilter::make('type')
                    ->label('Тип')
                    ->placeholder('Все типы')
                    ->options(Types::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
