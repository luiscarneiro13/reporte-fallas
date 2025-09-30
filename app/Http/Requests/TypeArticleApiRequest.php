<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TypeArticleApiRequest extends FormRequest
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
            'name' => [
                'required', 'unique:type_articles,name'
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'El nombre "' . $this->name . '" ya está en uso para esta sucursal.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $data['data'] = $validator->errors()->first();
        $data['success'] = false;
        throw new HttpResponseException(response()->json($data, 200));
    }
}
