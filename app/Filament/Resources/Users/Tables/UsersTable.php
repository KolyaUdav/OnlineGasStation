<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\Roles\Entities;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Имя')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Дата регистрации')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('role_id')
                    ->label('Роль')
                    ->badge()
                    ->sortable(),
                TextColumn::make('balance.amount')
                    ->label('Баланс, BYN')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->schema([
                        DateTimePicker::make('created_at_start')->label('С'),
                        DateTimePicker::make('created_at_end')->label('По'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_at_start'], fn ($q, $date) => $q->where('created_at', '>=', $date))
                            ->when($data['created_at_end'], fn ($q, $date) => $q->where('created_at', '<=', $date));
                    }),

                SelectFilter::make('role_id')
                    ->options(Entities::class)
                    ->multiple()
                    ->placeholder('Все роли')
                    ->label('Наименование роли'),

                Filter::make('balance_range')
                    ->schema([
                        TextInput::make('amount_start')
                            ->label('С')
                            ->numeric()
                            ->prefix('BYN'),
                        TextInput::make('amount_end')
                            ->label('До')
                            ->numeric()
                            ->prefix('BYN'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $hasStart = filled($data['amount_start'] ?? null);
                        $hasEnd = filled($data['amount_end'] ?? null);

                        if (!$hasStart && !$hasEnd) {
                            return $query;
                        }

                        // TODO: потенциально тяжелый запрос при наличии большого кол-ва юзеров
                        return $query->whereHas('balance', function ($query) use ($hasStart, $hasEnd, $data) {
                            return $query->when($hasStart, fn ($q) => $q->where('amount', '>=', $data['amount_start']))
                                ->when($hasEnd, fn ($q) => $q->where('amount', '<=', $data['amount_end']));
                        });
                    }),
            ])
            ->filtersFormSchema(fn (array $filters): array => [
                Section::make('Период регистрации')
                    ->schema([
                        $filters['created_at'],
                    ]),

                Section::make('Роли')
                    ->schema([
                        $filters['role_id'],
                    ]),

                Section::make('Баланс')
                    ->schema([
                        $filters['balance_range'],
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
