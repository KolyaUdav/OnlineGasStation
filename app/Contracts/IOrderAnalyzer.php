<?php

namespace App\Contracts;

use App\DTOs\ReportDTO;

interface IOrderAnalyzer
{
    public function getResult(ReportDTO $dto): array;
}
