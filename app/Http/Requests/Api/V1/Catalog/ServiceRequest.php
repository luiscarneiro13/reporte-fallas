<?php

namespace App\Http\Requests\Api\V1\Catalog;

use App\Helpers\BranchHelper;
use App\Http\Requests\Api\V1\ApiFormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string',
                Rule::unique('services', 'name')
                    ->where('branch_id', BranchHelper::getBranchId())
                    ->ignore($this->route('id')),
            ],
            // El original no validaba price (spec §6.3.5) — se agrega.
            'price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
