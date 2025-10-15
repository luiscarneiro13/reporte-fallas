<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurationRequest extends FormRequest
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
            'tax' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'tax.required' => 'El mínimo % de impuesto es cero y debe ser numérico',
            'tax.numeric' => 'El mínimo % de impuesto es cero y debe ser numérico',
            'tax.min' => 'El mínimo % de impuesto es cero y debe ser numérico',
            'discount.required' => 'El mínimo % de descuento es cero y debe ser numérico',
            'discount.numeric' => 'El mínimo % de descuento es cero y debe ser numérico',
            'discount.min' => 'El mínimo % de descuento es cero y debe ser numérico',
        ];
    }
}
