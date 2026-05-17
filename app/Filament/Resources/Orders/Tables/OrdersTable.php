<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fuel_name')
                    ->label('Название топлива')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->label('Количество')
                    ->sortable(),
                TextColumn::make('cost_in_time')
                    ->numeric()
                    ->label('Стоимость единицы')
                    ->sortable(),
                TextColumn::make('cost')
                    ->label('Стоимость заказа')
                    ->money('BYN')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Заказчик')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime('d.m.Y H:i:s')
                    ->label('Дата и время')
                    ->sortable(),
            ])
            ->filters([
                //
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
