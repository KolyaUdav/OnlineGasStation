<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected ?string $heading = 'Редактирование заказа';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('fuel_name')
                ->label('Название топлива'),

            TextInput::make('quantity')
                ->label('Количество')
                ->required(),

            TextInput::make('cost_in_time')
                ->label('Стоимость единицы')
                ->required(),

            TextInput::make('cost')
                ->label('Стоимость')
                ->prefix('BYN')
                ->required(),

            Select::make('user_id')
                ->label('Пользователь')
                ->relationship('user', 'name'),

            TextInput::make('check_path')
                ->label('Путь к чеку')
                ->disabled(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
