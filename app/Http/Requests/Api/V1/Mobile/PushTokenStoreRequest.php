<?php

namespace App\Http\Requests\Api\V1\Mobile;

use App\Http\Requests\Api\V1\ApiFormRequest;
use Illuminate\Validation\Rule;

class PushTokenStoreRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'max:512'],
            'platform' => ['required', 'string', Rule::in(['android', 'ios'])],
            'device_id' => ['nullable', 'string', 'max:191'],
            'app_version' => ['nullable', 'string', 'max:50'],
        ];
    }
}
