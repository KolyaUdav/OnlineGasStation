<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DateTimePicker;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;

class OrderReport extends Page
{
    protected static ?string $navigationLabel = 'Отчет по заказам';
    protected string $view = 'filament.pages.order-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'start_date_time' => now()->startOfMonth()->format('d.m.Y H:i:s'),
            'end_date_time' => now()->endOfMonth()->format('d.m.Y H:i:s'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            DateTimePicker::make('start_date_time')
                ->label('Дата и время с')
                ->required(),
            DateTimePicker::make('end_date_time')
                ->label('Дата и время по')
                ->required(),
        ])
        ->columns(2)
        ->statePath($this->data);
    }
}
