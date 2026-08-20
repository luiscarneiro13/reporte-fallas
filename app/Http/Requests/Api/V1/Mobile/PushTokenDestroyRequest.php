<?php

namespace App\Http\Requests\Api\V1\Mobile;

use App\Http\Requests\Api\V1\ApiFormRequest;

class PushTokenDestroyRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'max:512'],
        ];
    }
}
