<?php

namespace App\Enums\Reports;

enum Statuses: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
}
