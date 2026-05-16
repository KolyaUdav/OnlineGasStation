<?php

namespace App\Enums\Reports;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Statuses: string implements HasLabel, HasColor
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Ожидание',
            self::Processing => 'В процессе',
            self::Completed => 'Выполнен',
            self::Failed => 'Не выполнен',
            default => '',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Processing => 'warning',
            self::Completed => 'success',
            self::Failed => 'danger',
            default => '',
        };
    }
}
