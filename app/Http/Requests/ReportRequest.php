<?php

namespace App\Http\Requests;

use App\Enums\Reports\Types;
use App\Models\Report;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            Report::FIELD_TYPE => [Rule::enum(Types::class)],

            // Фильтры
            'date_from' => 'required|date|before_or_equal:today',
            'date_to' => 'required|date|after_or_equal:date_from',
        ];
    }

    public function messages(): array
    {
        return [
            Report::FIELD_TYPE => 'Неизвестный тип отчета',

            // Фильтры
            'date_from' => 'Невалидный date_from',
            'date_to' => 'Невалидный date_to',
        ];
    }
}
