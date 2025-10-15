<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
                Rule::unique('products')->ignore($this->id)->where(function ($query) {
                    return $query->where('name', $this->name)
                        ->where('branch_id', session('branch')->id);
                }),
            ],
            // 'available_qty' => 'required|numeric',
            // 'price' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'brand_id' => 'required|exists:brands,id',
            'type_article_id' => 'required|exists:type_articles,id'
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'El nombre "' . $this->name . '" ya está en uso para esta sucursal.',
            'available_qty.required' => 'La cantidad es requerida',
            'available_qty.numeric' => 'La cantidad debe ser un número',
        ];
    }
}
