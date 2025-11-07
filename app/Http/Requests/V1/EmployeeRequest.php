<?php

namespace App\Http\Requests\V1;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtiene el ID del empleado para ignorarlo en la validación 'unique' (en caso de edición)
        $employeeId = (int) $this->id;
        // Identificamos si es una operación de CREACIÓN (POST)
        $isCreating = $this->method() === 'POST';

        return [
            // --- DATOS BÁSICOS (Requeridos) ---
            'identification_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees')->ignore($employeeId),
            ],
            'rif' => 'nullable|string|max:100',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',

            // --- DATOS BÁSICOS (Requeridos según tu código original) ---
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',

            // Asumo que 'position' y 'executor' también deberían ir aquí:
            'position' => 'nullable|string|max:255',
            'executor' => 'nullable|integer',

            // --- USUARIO DE SISTEMA (Condicionales) ---
            'email' => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('employees')->ignore($employeeId),
            ],

            // Contraseña: CORRECCIÓN CLAVE para edición
            'password' => [
                'nullable', // Permite que el campo esté vacío en la edición (PUT)
                'string',
                'min:6',
                'max:255',

                // CRÍTICO: La contraseña es requerida solo si es CREACIÓN (POST) Y se proporciona email.
                Rule::when($isCreating && $this->filled('email'), [
                    'required',
                ]),
                // En edición, si el campo está vacío, 'nullable' lo ignora. Si se rellena, se aplican min/max.
            ],

            // Rol: Condicionalmente requerido e invalidado si tiene valor '0' cuando se proporciona email.
            'role_id' => [
                'integer',
                Rule::when($this->filled('email'), [
                    'required',
                    'exists:roles,id',
                    'not_in:0',  // <-- Si hay email, el valor NO puede ser '0'
                ], [
                    'nullable',
                    'in:0',    // <-- Si NO hay email, el valor DEBE ser '0'
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        // ESTA SECCIÓN HA SIDO RESTAURADA ÍNTEGRAMENTE
        return [
            // --- identification_number Rules ---
            'identification_number.required' => 'La cédula es obligatoria.',
            'identification_number.string' => 'La cédula debe ser una cadena de texto.',
            'identification_number.max' => 'La cédula no puede exceder los 20 caracteres.',
            'identification_number.unique' => 'Esta cédula ya está registrada en la base de datos.',

            // --- first_name Rules ---
            'first_name.required' => 'El nombre es obligatorio.',
            'first_name.string' => 'El nombre debe ser una cadena de texto.',
            'first_name.max' => 'El nombre no puede exceder los 100 caracteres.',

            // --- last_name Rules ---
            'last_name.required' => 'El apellido es obligatorio.',
            'last_name.string' => 'El apellido debe ser una cadena de texto.',
            'last_name.max' => 'El apellido no puede exceder los 100 caracteres.',

            // --- email Rules (Opcional) ---
            'email.email' => 'Debe ingresar un formato de correo electrónico válido.',
            'email.max' => 'El correo electrónico no puede exceder los 150 caracteres.',
            'email.unique' => 'Este correo electrónico ya está asociado a otro empleado.',
            // Nota: 'email.string' no es necesario si ya tienes 'email' y 'nullable'

            // --- phone_number Rules ---
            'phone_number.required' => 'El teléfono es obligatorio.',
            'phone_number.string' => 'El teléfono debe ser una cadena de texto.',
            'phone_number.max' => 'El teléfono no puede exceder los 20 caracteres.',

            // --- address Rules ---
            'address.string' => 'La dirección debe ser un texto válido.',

            // --- USUARIO DE SISTEMA CONDICIONAL ---
            // El mensaje 'password.required_with' se disparará por la regla 'required' condicional
            'password.required_with' => 'La Contraseña temporal es obligatoria si se proporciona el Email.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La Contraseña debe tener al menos 8 caracteres.', // NOTA: Tu regla es min:6, tu mensaje es min:8.
            'password.max' => 'La Contraseña no puede exceder los 255 caracteres.',

            'role_id.required_with' => 'Debe seleccionar un Rol de sistema si proporciona el Email.',
            'role_id.integer' => 'El Rol de sistema no es válido.',
            'role_id.exists' => 'Debe selecionar un rol.',
        ];
    }
}
