<?php

namespace App\Listeners;

use App\Events\OrdersReportBuilt;
use App\Models\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleAfterOrdersReportBuilt
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrdersReportBuilt $event): void
    {
        $id = $event->getId();
        $data = $event->getData();

        $report = Report::find($id);

        if (!$report) {
            Log::error("Отчет по ID $id не был найден в БД");

            return;
        }

        $report->update($data);
    }
}
