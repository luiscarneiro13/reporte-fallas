<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
            'name' => 'required|string|min:3',
            'description' => 'nullable|string|min:3',
            'customer_id' => 'required|exists:customers,id',
            'division_id' => 'required|exists:divisions,id',
            'geographic_area' => 'required|string|min:3',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Reglas para el campo 'name'
            'name.required' => 'El campo Nombre del proyecto es obligatorio.',
            'name.string' => 'El Nombre del proyecto debe ser texto.',
            'name.min' => 'El Nombre del proyecto debe tener al menos 3 caracteres.',

            // Reglas para el campo 'description'
            'description.string' => 'La Descripción debe ser texto.',
            'description.min' => 'La Descripción debe tener al menos 3 caracteres.',

            // Reglas para el campo 'customer_id'
            'customer_id.required' => 'Debe seleccionar un Cliente.',
            'customer_id.exists' => 'El Cliente seleccionado no es válido.',

            // Reglas para el campo 'division_id'
            'division_id.required' => 'Debe seleccionar una División.',
            'division_id.exists' => 'La División seleccionada no es válida.',

            // Reglas para el campo 'geographic_area'
            'geographic_area.required' => 'El campo Área geográfica es obligatorio.',
            'geographic_area.string' => 'El Área geográfica debe ser texto.',
            'geographic_area.min' => 'El Área geográfica debe tener al menos 3 caracteres.',
        ];
    }
}
