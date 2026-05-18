<?php

namespace App\Filament\Resources\Promotions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PromotionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->default('Заголовок акции'),
                Textarea::make('text')
                    ->columnSpanFull(),
                TextInput::make('sale_percent')
                    ->required()
                    ->numeric()
                    ->default(1),
                DateTimePicker::make('date_start')
                    ->required(),
                DateTimePicker::make('date_end')
                    ->required(),
                TextInput::make('conditions'),
            ]);
    }
}
