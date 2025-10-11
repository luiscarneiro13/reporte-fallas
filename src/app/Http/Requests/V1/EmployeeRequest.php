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
        // Nota: Si usas $this->id en tu controlador de edición, mantén (int) $this->id;

        return [
            // --- DATOS BÁSICOS (Requeridos) ---
            'identification_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees')->ignore($employeeId),
            ],
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',

            // --- DATOS BÁSICOS (Opcionales) ---
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',

            // --- USUARIO DE SISTEMA (Condicionales) ---
            // El campo 'email' es el que dispara la validación condicional
            'email' => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('employees')->ignore($employeeId),
            ],

            // Contraseña: requerida si el campo 'email' fue proporcionado.
            'password' => [
                // Si el campo email NO está vacío (es decir, el usuario intenta crear un usuario de sistema)
                Rule::when($this->filled('email'), [
                    'required',
                    'string',
                    'min:6',
                    'max:255',
                ], ['nullable']), // Si no se proporciona email o está vacío, 'password' no se valida como requerido.
            ],

            // Rol: Condicionalmente requerido e invalidado si tiene valor '0' cuando se proporciona email.
            'role_id' => [
                'integer',
                Rule::when($this->filled('email'), [
                    'required',
                    'exists:roles,id',
                    'not_in:0',   // <-- Si hay email, el valor NO puede ser '0' ("Sin usuario de sistema")
                ], [
                    'nullable',
                    'in:0',       // <-- Si NO hay email, el valor DEBE ser '0' (o nulo, lo cual se cubre con 'nullable')
                ]),
            ],
        ];
    }

    public function messages(): array
    {
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
            'password.required_with' => 'La Contraseña temporal es obligatoria si se proporciona el Email.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La Contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La Contraseña no puede exceder los 255 caracteres.',

            'role_id.required_with' => 'Debe seleccionar un Rol de sistema si proporciona el Email.',
            'role_id.integer' => 'El Rol de sistema no es válido.',
            'role_id.exists' => 'Debe selecionar un rol.',
        ];
    }
}
