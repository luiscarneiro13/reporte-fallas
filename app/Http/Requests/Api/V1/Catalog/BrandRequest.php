<?php

namespace App\Http\Requests\Api\V1\Catalog;

use App\Helpers\BranchHelper;
use App\Http\Requests\Api\V1\ApiFormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:90',
                Rule::unique('brands', 'name')
                    ->where('branch_id', BranchHelper::getBranchId())
                    ->ignore($this->route('id')),
            ],
        ];
    }
}
