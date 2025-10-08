<?php

namespace App\Http\Requests\V1\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DivisionApiRequest extends FormRequest
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
            'description' => 'required|string|min:3',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es requerido',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $data['data'] = $validator->errors();
        $data['success'] = false;
        throw new HttpResponseException(response()->json($data, 200));
    }
}
