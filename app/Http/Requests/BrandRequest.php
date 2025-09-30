<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
                'required',
                Rule::unique('brands')->ignore($this->id)->where(function ($query) {
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
