<?php

namespace App\Filament\Resources\Reports\Schemas;

use App\Enums\Reports\Statuses;
use App\Enums\Reports\Types;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('type')
                    ->options(Types::class)
                    ->required(),
                Select::make('status')
                    ->options(Statuses::class)
                    ->required(),
                TextInput::make('payload'),
                TextInput::make('file_path'),
                Textarea::make('error_message')
                    ->columnSpanFull(),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
