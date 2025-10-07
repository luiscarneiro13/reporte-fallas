<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DailyRateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rate' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'rate.required' => 'La tasa actual debe ser mayor que cero y debe ser numérico',
            'rate.numeric' => 'La tasa actual debe ser mayor que  cero y debe ser numérico',
            'rate.min' => 'La tasa actual debe ser mayor que  cero y debe ser numérico',
        ];
    }
}
