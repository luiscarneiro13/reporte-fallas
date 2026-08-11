<?php

namespace App\Http\Requests\Api\V1\Catalog;

use App\Http\Requests\Api\V1\ApiFormRequest;

/**
 * El original no tenía FormRequest (spec §6.3.4). Se agrega validación real.
 */
class SupplierRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:90'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:90'],
            'email' => ['nullable', 'email', 'max:75'],
        ];
    }
}
