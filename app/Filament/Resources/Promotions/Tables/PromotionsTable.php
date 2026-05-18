<?php

namespace App\Filament\Resources\Promotions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PromotionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Наименование')
                    ->searchable(),
                TextColumn::make('sale_percent')
                    ->label('Процент скидки')
                    ->suffix('%')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date_start')
                    ->label('Дата начала')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('date_end')
                    ->label('Дата окончания')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
