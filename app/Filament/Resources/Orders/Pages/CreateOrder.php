<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Contracts\IPriceHandler;
use App\Enums\Fuels;
use App\Events\OrderPlaced;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderHandler;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected ?string $heading = 'Создание заказа';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('fuel_type')
                ->options(Fuels::class)
                ->placeholder('Выберите тип')
                ->label('Тип топлива')
                ->reactive()
                ->afterStateUpdated(function (Get $get, Set $set) {
                    $code = $get('fuel_type')->value;

                    $set('fuel_name', Fuels::from($code)->getLabel());
                    $set('quantity', '0');
                    $set('cost', '0');

                    $priceHandler = app(IPriceHandler::class)->getPrice($code);
                    $price = $priceHandler->price;

                    $set('cost_in_time', $price);
                }),

            TextInput::make('fuel_name')
                ->label('Наименование топлива')
                ->readOnly(),

            TextInput::make('quantity')
                ->label('Количество')
                ->required()
                ->reactive()
                ->afterStateUpdated(function (Get $get, Set $set) {
                    if (!empty('fuel_type') && !empty($get('cost_in_time'))) {
                        $quantity = (int)$get('quantity');
                        $costInTime = (float)$get('cost_in_time');

                        $set('cost', round($quantity * $costInTime, 2));
                    } else {
                        $set('quantity', '');
                    }
                }),

            TextInput::make('cost_in_time')
                ->label('Стоимость за единицу')
                ->required()
                ->prefix('BYN')
                ->readOnly(),

            TextInput::make('cost')
                ->label('Стоимость')
                ->prefix('BYN')
                ->readOnly()
                ->required(),

            Select::make('user_id')
                ->label('Пользователь')
                ->placeholder('Выберите заказчика')
                ->relationship('user', 'name')
                ->required(),

            TextInput::make('check_path')
                ->label('Путь к чеку')
                ->readOnly(),
        ]);
    }

    protected function handleRecordCreation(array $data): Order
    {
        $userId = $data[Order::FIELD_USER_ID];
        $user = User::find($userId);

        return (new OrderHandler($data))->create($user);
    }

    protected function afterCreate(): void
    {
        event(new OrderPlaced($this->record));
    }
}
