<?php

namespace App\Http\Controllers;

use App\DTOs\ReportTaskDTO;
use App\Enums\Reports\Statuses;
use App\Http\Requests\ReportRequest;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ReportController extends BaseController
{
    const REDIS_QUEUE_TASKS = 'reports_tasks';
    const REDIS_QUEUE_COMPLETED = 'reports_completed';

    protected $model = Report::class;

    public function create(ReportRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $report = $this->model::apiAdd([
            Report::FIELD_USER_ID => $user->id,
            Report::FIELD_STATUS => Statuses::Pending,
            Report::FIELD_TYPE => $validated[Report::FIELD_TYPE],
            Report::FIELD_PAYLOAD => $validated,
        ]);

        $dto = ReportTaskDTO::fromModel($report);

        try {
            Redis::rpush(self::REDIS_QUEUE_TASKS, $dto->toJson());
        } catch (\Throwable $e) {
            $report->update([
                Report::FIELD_STATUS => Statuses::Failed,
                Report::FIELD_ERROR_MESSAGE => 'Сервис очередей недоступен',
            ]);

            Log::error("Redis push error: {$e->getMessage()}");

            return $this->error('Сервис очередей недоступен', 503);
        }

        return $this->success([
            'data' => [
                'id' => $report->id,
                'status' => Statuses::Processing->value,
            ],
        ]);
    }
}
