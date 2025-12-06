<?php

namespace App\Http\Requests;

use App\Models\Promotion;
use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            Promotion::FIELD_TITLE => 'required|string|max:100',
            Promotion::FIELD_TEXT => 'required|string',
            Promotion::FIELD_SALE_PERCENT => 'required|integer|min:1|max:100',
            Promotion::FIELD_DATE_START => 'required|date_format:Y-m-d H:i:s',
            Promotion::FIELD_DATE_END => 'required|date_format:Y-m-d H:i:s|after:' . Promotion::FIELD_DATE_START,
            Promotion::FIELD_CONDITIONS => 'nullable|array',
            Promotion::FIELD_CONDITIONS . '.' . Promotion::RULE_MIN_BALANCE => 'sometimes|numeric',
            Promotion::FIELD_CONDITIONS . '.' . Promotion::RULE_MIN_REG_DATE => 'sometimes|date_format:Y-m-d H:i:s',
            Promotion::FIELD_CONDITIONS . '.' . Promotion::RULE_FUEL_TYPES => 'sometimes|array',
            Promotion::FIELD_CONDITIONS . '.' . Promotion::RULE_MIN_ORDER_SUM => 'sometimes|numeric',
        ];
    }

    public function messages()
    {
        return [
            '*.required' => 'Все поля являются обязательными',
            
            Promotion::FIELD_TITLE . '.required' => 'Заголовок обязателен',
            Promotion::FIELD_TITLE . '.string' => 'Заголовок должен быть строкой',
            Promotion::FIELD_TITLE . '.max' => 'Заголовок не должен превышать 100 символов',
            
            Promotion::FIELD_TEXT . '.required' => 'Текст обязателен для заполнения',
            Promotion::FIELD_TEXT . '.string' => 'Текст должен быть строкой',

            Promotion::FIELD_SALE_PERCENT . '.required' => ' Процент обязателен для заполнения',
            Promotion::FIELD_SALE_PERCENT . '.integer' => 'Процент должен быть числом',
            Promotion::FIELD_SALE_PERCENT . '.max' => 'Процент не может быть выше 100',
            Promotion::FIELD_SALE_PERCENT . '.min' => 'Процент не может быть меньше 1',

            Promotion::FIELD_DATE_START . '.required' => 'Дата начала обязательна для заполнения',
            Promotion::FIELD_DATE_START . '.date_format' => 'Неверный формат даты и времени. Формат: Y-m-d H:i:s',

            Promotion::FIELD_DATE_END . '.required' => 'Дата окончания обязательна для заполнения',
            Promotion::FIELD_DATE_END . '.date_format' => 'Неверный формат даты и времени. Формат: Y-m-d H:i:s',
            Promotion::FIELD_DATE_END . '.after' => 'Дата окончания не может быть раньше даты начала',

            Promotion::FIELD_CONDITIONS . '.array' => 'Условия акции должны приходить в формате массива',
            Promotion::FIELD_CONDITIONS . '.' . Promotion::RULE_MIN_BALANCE . 'numeric' => 'Правило минимального баланса должно быть числом',
            Promotion::FIELD_CONDITIONS . '.' . Promotion::RULE_FUEL_TYPES . 'array' => 'Правило типов топлива должно быть массивом',
            Promotion::FIELD_CONDITIONS . '.' . Promotion::RULE_MIN_REG_DATE . 'date_format' => 'Правило времени регистрации должно быть в формате Y-m-d H:i:s',
            Promotion::FIELD_CONDITIONS . '.' . Promotion::RULE_MIN_ORDER_SUM . 'numeric' => 'Правило минимальной суммы заказа должно быть числом',
        ];
    }
}