<?php

namespace App\DTOs;

use App\Enums\Reports\Types;
use App\Models\Report;
use JsonSerializable;
use Override;

readonly class ReportTaskDTO implements JsonSerializable
{
    public function __construct(
        public string $reportId,
        public Types $type,
        public array $payload,
    ) {}

    public static function fromModel(Report $report): self
    {
        return new self(
            reportId: $report->id,
            type: $report->{Report::FIELD_TYPE},
            payload: $report->{Report::FIELD_PAYLOAD},
        );
    }

    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'reportId' => $this->reportId,
            'type' => $this->type->value,
            'payload' => $this->payload,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this);
    }
}
