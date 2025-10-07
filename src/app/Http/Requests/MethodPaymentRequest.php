<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MethodPaymentRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('method_payments')->ignore($this->id)->where(function ($query) {
                    return $query->where('name', $this->name)
                        ->where('branch_id', session('branch')->id);
                }),
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'El nombre "' . $this->name . '" ya está en uso para esta sucursal.',
        ];
    }
}
