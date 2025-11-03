<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <-- Importante: Añadimos la clase Rule

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // Determina si estamos en una operación de edición (PUT/PATCH)
        $isUpdate = $this->method() === 'PUT' || $this->method() === 'PATCH';

        // ----------------------------------------------------
        // Lógica de Email con exclusión para UPDATE (Soporta múltiples rutas)
        // ----------------------------------------------------

        $emailRules = [
            'required',
            'email',
            'min:3',
        ];

        // 1. Identificamos los posibles nombres de los parámetros de ruta para la edición
        // NOTA: Los nombres de los parámetros de ruta de Route::resource son por defecto
        // el singular del recurso. Ejemplo: /operadores/{operadore}
        $routeParameters = ['operadore', 'supervisore', 'administradore'];
        $userId = null;

        // 2. Buscamos el valor del parámetro activo en la ruta actual
        foreach ($routeParameters as $param) {
            $value = $this->route($param);
            if ($value) {
                $userId = $value;
                break;
            }
        }

        // 3. Aplicamos la regla de unicidad ignorando el registro actual
        if ($isUpdate && $userId) {
            // Usamos Rule::unique para excluir el registro actual por su clave primaria
            $emailRules[] = Rule::unique('users', 'email')->ignore($userId);
        } else {
            // Si estamos creando (POST), simplemente verificamos la unicidad
            $emailRules[] = 'unique:users,email';
        }

        // ----------------------------------------------------
        // Lógica Condicional para Contraseñas
        // ----------------------------------------------------

        // La contraseña es requerida en creación, opcional en edición
        $passwordRules = $isUpdate ? ['nullable', 'min:6'] : ['required', 'min:6'];

        // La confirmación es requerida si el campo 'password' fue enviado
        $passwordConfirmationRules = [
            'min:6',
            'same:password',
            'required_with:password',
        ];


        return [
            'name'                  => ['required', 'string', 'min:3'],
            'email'                 => $emailRules,
            'password'              => $passwordRules,
            'password_confirmation' => $passwordConfirmationRules,
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * @return array
     */
    public function messages(): array
    {
        return [
            // Name
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string'   => 'El nombre debe ser una cadena de texto.',
            'name.min'      => 'El nombre debe tener al menos :min caracteres.',

            // Email
            'email.required' => 'El campo email es obligatorio.',
            'email.email'    => 'Debe introducir un formato de correo electrónico válido.',
            'email.unique'   => 'Ya existe un usuario registrado con este email.',
            'email.min'      => 'El email debe tener al menos :min caracteres.',

            // Password
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos :min caracteres.',

            // Password Confirmation
            'password_confirmation.required_with' => 'Debe confirmar la contraseña.',
            'password_confirmation.same'          => 'Las contraseñas no coinciden.',
            'password_confirmation.min'           => 'La confirmación de la contraseña debe tener al menos :min caracteres.',
        ];
    }
}
