<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerApiRequest extends FormRequest
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
            'name' => ['required'],
            'cedula' => ['required', 'unique:users,cedula'],
            'address' => ['required'],
            'email' => ['nullable', 'email', 'unique:users,email'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es requerido',
            'email.email' => 'El email no tiene un formato válido',
            'email.unique' => 'Ya existe un cliente con este correo',
            'cedula.required' => 'La cédula es requerida',
            'cedula.unique' => 'Ya existe un cliente con esta cédula',
            'address.required' => 'La dirección es requerida',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $data['data'] = $validator->errors();
        $data['success'] = false;
        throw new HttpResponseException(response()->json($data, 200));
    }
}
