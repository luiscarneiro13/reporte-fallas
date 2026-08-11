<?php

namespace App\Http\Requests\Api\V1\AdminBranch;

use App\Http\Requests\Api\V1\ApiFormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends ApiFormRequest
{
    public function rules(): array
    {
        $employeeId = $this->route('id');
        $isCreating = $this->isMethod('POST');

        return [
            'identification_number' => [
                'required', 'string', 'max:20',
                Rule::unique('employees', 'identification_number')->ignore($employeeId),
            ],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'position' => ['nullable', 'string', 'max:255'],
            'executor' => ['nullable', 'integer'],
            'email' => [
                'nullable', 'email', 'max:150',
                Rule::unique('employees', 'email')->ignore($employeeId),
            ],
            'password' => [
                'nullable', 'string', 'min:6', 'max:255',
                Rule::requiredIf($isCreating && $this->filled('email')),
            ],
            'role_id' => [
                'integer',
                Rule::when($this->filled('email'), ['required', 'exists:roles,id', 'not_in:0'], ['nullable', 'in:0']),
            ],
        ];
    }
}
