<?php

namespace App\Livewire;

use App\Enums\Reports\Statuses;
use App\Models\Report;
use Livewire\Component;

class OrdersReportResult extends Component
{
    public Report $record;

    public function render()
    {
        return view('livewire.orders-report-result');
    }
}
