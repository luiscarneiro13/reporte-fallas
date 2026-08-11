<?php

namespace App\Http\Requests\Api\V1\Catalog;

use App\Http\Requests\Api\V1\ApiFormRequest;
use Illuminate\Validation\Rule;

class UserRoleRequest extends ApiFormRequest
{
    public function rules(): array
    {
        $isCreating = $this->isMethod('POST');
        $userId = $this->route('id');

        return [
            'name' => ['required', 'string', 'min:3'],
            'email' => [
                'required', 'email', 'min:3',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => $isCreating ? ['required', 'min:6'] : ['nullable', 'min:6'],
            'password_confirmation' => ['required_with:password', 'same:password', 'min:6'],
        ];
    }
}
