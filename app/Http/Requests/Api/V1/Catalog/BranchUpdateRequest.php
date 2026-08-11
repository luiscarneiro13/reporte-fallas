<?php

namespace App\Http\Requests\Api\V1\Catalog;

use App\Http\Requests\Api\V1\ApiFormRequest;
use Illuminate\Validation\Rule;

class BranchUpdateRequest extends ApiFormRequest
{
    public function rules(): array
    {
        $branchId = $this->route('id');

        return [
            'name' => ['required', 'string', 'min:3', Rule::unique('branches', 'name')->ignore($branchId)],
            'rif' => ['required', 'string', 'min:3'],
            'address' => ['nullable', 'string', 'min:3'],
            'description' => ['nullable', 'string', 'min:3'],
            'phone' => ['required', 'numeric'],
            // El original validaba email contra la tabla `users` (bug, spec §6.3.10) — corregido a `branches`.
            'email' => ['required', 'email', 'min:3', Rule::unique('branches', 'email')->ignore($branchId)],
            'logo' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
