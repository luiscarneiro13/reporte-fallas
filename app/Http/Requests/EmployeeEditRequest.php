<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeEditRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3'],
            'cedula' => ['required', 'min:3', Rule::unique('users')->ignore($this->id)->where(function ($query) {
                return $query->where('cedula', $this->cedula);
            })],
            'email' => ['required', 'min:3', Rule::unique('users')->ignore($this->id)->where(function ($query) {
                return $query->where('email', $this->email);
            })],
            'password' => 'nullable|min:6',
            'password_confirmation' => 'nullable|required_with:password|same:password|min:6',
        ];
    }
}
