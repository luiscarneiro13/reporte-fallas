<?php

namespace App\Http\Requests\Api\V1\AdminBranch;

use App\Http\Requests\Api\V1\ApiFormRequest;

/**
 * En el original (CustomerRequest web) esta validación existía pero no
 * estaba conectada al controlador (spec §6.2.1: "no hay validación real").
 * Aquí sí se aplica.
 */
class CustomerRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:90'],
            'rif' => ['required', 'string', 'max:90'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:90'],
            'email' => ['nullable', 'email', 'max:90'],
        ];
    }
}
