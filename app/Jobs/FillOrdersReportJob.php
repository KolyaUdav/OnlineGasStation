<?php

namespace App\Jobs;

use App\Contracts\IOrderAnalyzer;
use App\DTOs\ReportDTO;
use App\Enums\Reports\Statuses;
use App\Events\OrdersReportBuilt;
use App\Models\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FillOrdersReportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $id,
        private ReportDTO $dto,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(IOrderAnalyzer $analyzer): void
    {
        $result = $analyzer->getResult($this->dto);

        if (!empty($result)) {
            $data = [
                Report::FIELD_STATUS => Statuses::Completed,
                Report::FIELD_PAYLOAD => $result,
                Report::FIELD_COMPLETED_AT => now(),
            ];
        } else {
            $data = [
                Report::FIELD_STATUS => Statuses::Failed,
                Report::FIELD_ERROR_MESSAGE => 'Пустой результат отчета',
                Report::FIELD_COMPLETED_AT => now(),
            ];
        }

        OrdersReportBuilt::dispatch($this->id, $data);
    }

    public function failed(\Throwable $exception): void
    {
        $data = [
            Report::FIELD_STATUS => Statuses::Failed,
            Report::FIELD_ERROR_MESSAGE => $exception->getMessage(),
            Report::FIELD_COMPLETED_AT => now(),
        ];

        OrdersReportBuilt::dispatch($this->id, $data);
    }
}
