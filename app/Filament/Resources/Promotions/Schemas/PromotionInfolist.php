<?php

namespace App\Filament\Resources\Promotions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PromotionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('text')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('sale_percent')
                    ->numeric(),
                TextEntry::make('date_start')
                    ->dateTime(),
                TextEntry::make('date_end')
                    ->dateTime(),
            ]);
    }
}
