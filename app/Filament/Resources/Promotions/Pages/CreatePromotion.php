<?php

namespace App\Filament\Resources\Promotions\Pages;

use App\Enums\Fuels;
use App\Filament\Resources\Promotions\PromotionResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CreatePromotion extends CreateRecord
{
    protected static string $resource = PromotionResource::class;

    protected ?string $heading = 'Создать акцию';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Основная информация')
                ->schema([
                TextInput::make('title')
                    ->label('Заголовок')
                    ->required(),
                
                Textarea::make('text')
                    ->label('Описание акции'),

                TextInput::make('sale_percent')
                    ->label('Процент скидки')
                    ->prefix('%')
                    ->required(),

                DateTimePicker::make('date_start')
                    ->label('Дата начала')
                    ->format('d.m.Y H:i:s')
                    ->required(),

                DateTimePicker::make('date_end')
                    ->label('Дата окончания')
                    ->format('d.m.Y H:i:s')
                    ->required(),
            ]),

            Section::make('Условия акции')
                ->schema([
                    TextInput::make('conditions.min_order_sum')
                        ->label('Минимальная сумма заказа')
                        ->numeric()
                        ->prefix('BYN'),

                    TextInput::make('conditions.min_balance')
                        ->label('Минимальная баланс')
                        ->numeric()
                        ->prefix('BYN'),

                    Select::make('conditions.fuel_types')
                        ->label('Типы топлива')
                        ->options(Fuels::class)
                        ->placeholder('Выберите')
                        ->multiple(),

                    DateTimePicker::make('conditions.min_reg_date')
                        ->label('Дата регистрации от')
                        ->format('d.m.Y H:i:s'),
                ]),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $incomingFormat = 'd.m.Y H:i:s';

        if (!empty($data['date_start'])) {
            $data['date_start'] = \DateTime::createFromFormat($incomingFormat, $data['date_start'])->format('Y-m-d H:i:s');
        }
        
        if (!empty($data['date_end'])) {
            $data['date_end'] = \DateTime::createFromFormat($incomingFormat, $data['data_end'] ?? $data['date_end'])->format('Y-m-d H:i:s');
        }

        if (!empty($data['conditions']['min_reg_date'])) {
            $data['conditions']['min_reg_date'] = \DateTime::createFromFormat($incomingFormat, $data['conditions']['min_reg_date'])->format('Y-m-d H:i:s');
        }

        return $data;
    }
}
