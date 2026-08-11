<?php

namespace App\Http\Requests\Api\V1\AdminBranch;

use App\Http\Requests\Api\V1\ApiFormRequest;

class FaultStatusRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
        ];
    }
}
