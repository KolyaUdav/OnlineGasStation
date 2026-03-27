<?php

namespace App\Enums;

enum Fuels: string
{
    case PBA = 'pba';
    case DtMin32 = 'dt-min32';
    case Dt = 'dt';
    case Ai98 = 'ai-98';
    case Ai95 = 'ai-95';
    case Ai92 = 'ai-92';
    case DtEco = 'dt-eco';

    public function getName(): string
    {
        return match ($this) {
            self::Ai95 => 'АИ-95',
            self::Ai92 => 'АИ-92',
            self::Ai98 => 'АИ-98',
            self::Dt => 'ДТ',
            self::DtMin32 => 'ДТ -32°',
            self::DtEco => 'ДТ ECO',
            self::PBA => 'ПБА',
            default => '',
        };
    }
}