<?php

namespace App\Http\Requests\Api\V1\Catalog;

use App\Helpers\BranchHelper;
use App\Http\Requests\Api\V1\ApiFormRequest;
use Illuminate\Validation\Rule;

class TypeArticleRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'min:3',
                Rule::unique('type_articles', 'name')
                    ->where('branch_id', BranchHelper::getBranchId())
                    ->ignore($this->route('id')),
            ],
        ];
    }
}
