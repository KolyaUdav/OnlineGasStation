<?php

namespace App\Filament\Resources\Promotions;

use App\Enums\Fuels;
use App\Filament\Resources\Promotions\Pages\CreatePromotion;
use App\Filament\Resources\Promotions\Pages\EditPromotion;
use App\Filament\Resources\Promotions\Pages\ListPromotions;
use App\Filament\Resources\Promotions\Pages\ViewPromotion;
use App\Filament\Resources\Promotions\Schemas\PromotionForm;
use App\Filament\Resources\Promotions\Schemas\PromotionInfolist;
use App\Filament\Resources\Promotions\Tables\PromotionsTable;
use App\Models\Promotion;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Акции';

    public static function form(Schema $schema): Schema
    {
        return PromotionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Заголовок'),
                        
                        TextEntry::make('text')
                            ->label('Описание акции'),

                        TextEntry::make('sale_percent')
                            ->label('Процент скидки')
                            ->suffix('%')
                            ->color('primary'),

                        TextEntry::make('date_start')
                            ->label('Дата начала')
                            ->dateTime('d.m.Y H:i:s'),

                        TextEntry::make('date_end')
                            ->label('Дата окончания')
                            ->dateTime('d.m.Y H:i:s'),
                    ]),

                Section::make('Условия акции')
                    ->schema([
                        TextEntry::make('conditions.min_order_sum')
                            ->label('Минимальная сумма заказа')
                            ->suffix(' BYN')
                            ->color('primary'),

                        TextEntry::make('conditions.min_balance')
                            ->label('Минимальная баланс')
                            ->suffix(' BYN')
                            ->color('primary'),

                        TextEntry::make('conditions.fuel_types')
                            ->label('Типы топлива')
                            ->badge()
                            ->formatStateUsing(function ($state) {
                                return Fuels::tryFrom($state)?->getLabel() ?? $state;
                            }),

                        TextEntry::make('conditions.min_reg_date')
                            ->label('Дата регистрации от')
                            ->dateTime('d.m.Y H:i:s'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return PromotionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromotions::route('/'),
            'create' => CreatePromotion::route('/create'),
            'view' => ViewPromotion::route('/{record}'),
            'edit' => EditPromotion::route('/{record}/edit'),
        ];
    }
}
