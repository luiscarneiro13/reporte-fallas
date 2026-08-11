<?php

namespace App\Http\Requests\Api\V1\Catalog;

use App\Helpers\BranchHelper;
use App\Http\Requests\Api\V1\ApiFormRequest;
use Illuminate\Validation\Rule;

/**
 * El original no validaba nada (spec §6.3.2). Se agrega validación real.
 */
class ModelVehicleRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:90',
                Rule::unique('model_vehicles', 'name')
                    ->where('branch_id', BranchHelper::getBranchId())
                    ->ignore($this->route('id')),
            ],
        ];
    }
}
