<?php

namespace App\Http\Requests;

use App\Enums\Fuels;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class OrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'fuel_type' => ['required', Rule::enum(Fuels::class)],
            'quantity' => 'required|int|min:1',
        ];
    }

    public function messages()
    {
        return [
            '*.required' => 'Все поля являются обязательными',
            
            'fuel_type.required' => 'Тип топлива обязателен для заполнения',
            
            'quantity.required' => 'Количество обязательно для заполнения',
            'quantity.integer' => 'Количество должно быть числом',
            'quantity.min' => 'Количество топлива не может быть меньше 1',
        ];
    }

    #[Override]
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        if (isset($validated['fuel_type'])) {
            $validated['fuel_type'] = $this->enum('fuel_type', Fuels::class);
        }

        return $validated;
    }
}