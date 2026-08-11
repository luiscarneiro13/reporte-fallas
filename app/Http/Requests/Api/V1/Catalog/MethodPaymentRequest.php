<?php

namespace App\Http\Requests\Api\V1\Catalog;

use App\Helpers\BranchHelper;
use App\Http\Requests\Api\V1\ApiFormRequest;
use Illuminate\Validation\Rule;

class MethodPaymentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string',
                Rule::unique('method_payments', 'name')
                    ->where('branch_id', BranchHelper::getBranchId())
                    ->ignore($this->route('id')),
            ],
            // El original no validaba currency (spec §6.3.6) — se agrega.
            'currency' => ['nullable', 'string', 'max:10'],
        ];
    }
}
