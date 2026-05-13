<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('fuel_name'),
                TextInput::make('fuel_type'),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('cost_in_time')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('cost')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                Textarea::make('check_path')
                    ->columnSpanFull(),
            ]);
    }
}
