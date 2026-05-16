<?php

namespace App\Enums\Reports;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Types: string implements HasLabel, HasColor
{
    case GeneralAnalytics = 'general_analytics';

    public function getLabel(): string
    {
        return match ($this) {
            self::GeneralAnalytics => 'Общая аналитика',
            default => '',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::GeneralAnalytics => 'warning',
            default => '',
        };
    }
}
