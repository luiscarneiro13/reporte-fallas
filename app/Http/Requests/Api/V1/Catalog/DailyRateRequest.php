<?php

namespace App\Http\Requests\Api\V1\Catalog;

use App\Http\Requests\Api\V1\ApiFormRequest;

class DailyRateRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'rate' => ['required', 'numeric', 'min:0'],
            // El original no validaba average_rate (spec §6.3.12) — se agrega.
            'average_rate' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
