<?php

namespace App\Http\Requests\Api\V1\AdminBranch;

use App\Http\Requests\Api\V1\ApiFormRequest;
use Illuminate\Validation\Rule;

class ExecutorRequest extends ApiFormRequest
{
    public function rules(): array
    {
        $employeeId = $this->route('id');

        return [
            'identification_number' => [
                'required', 'string', 'max:20',
                Rule::unique('employees', 'identification_number')->ignore($employeeId),
            ],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'external' => ['nullable', 'boolean'],
        ];
    }
}
