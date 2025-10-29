<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * * @return bool
     */
    public function authorize(): bool
    {
        // Generalmente, si usas permisos (Spatie), la autorización se hace en el controlador o middleware.
        // Aquí lo dejaremos en true para que el Request sea ejecutado.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // Determina si estamos en una operación de edición (UPDATE)
        // Esto se asume si el método HTTP es PUT o PATCH.
        $isUpdate = $this->method() === 'PUT' || $this->method() === 'PATCH';

        // Reglas de Email: ignora el email del usuario actual en las validaciones de unicidad si estamos editando.
        $emailRules = [
            'required',
            'email',
            'min:3',
        ];

        if ($isUpdate && $this->route('administrador')) {
            // Asume que la clave de la ruta es 'administrador' y usa su ID.
            // Si el nombre de tu parámetro de ruta es diferente, cámbialo aquí.
            $userId = $this->route('administrador')->id;
            $emailRules[] = 'unique:users,email,' . $userId;
        } else {
            $emailRules[] = 'unique:users,email';
        }


        // ----------------------------------------------------
        // Lógica Condicional para Contraseñas
        // ----------------------------------------------------

        // 1. Password: Es requerido solo si se está creando (no en edición)
        $passwordRules = $isUpdate ? ['nullable', 'min:6'] : ['required', 'min:6'];

        // 2. Password Confirmation:
        //    - En edición, es requerido si se envió el campo 'password'.
        //    - En creación, es requerido (porque 'password' es requerido).
        $passwordConfirmationRules = [
            'min:6',
            'same:password',
            // required_with:password hace que sea requerido si 'password' está presente
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
     * * @return array
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
