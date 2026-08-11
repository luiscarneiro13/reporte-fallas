<?php

namespace App\Http\Requests\Api\V1\AdminBranch;

use App\Http\Requests\Api\V1\ApiFormRequest;

class ProjectRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
            'description' => ['nullable', 'string', 'min:3'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'division_id' => ['required', 'integer', 'exists:divisions,id'],
            'geographic_area' => ['required', 'string', 'min:3'],
            'contract_number' => ['nullable', 'string', 'max:90'],
        ];
    }
}
