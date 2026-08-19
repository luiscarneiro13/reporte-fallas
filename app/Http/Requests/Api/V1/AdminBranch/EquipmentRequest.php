<?php

namespace App\Http\Requests\Api\V1\AdminBranch;

use App\Http\Requests\Api\V1\ApiFormRequest;
use Illuminate\Validation\Rule;

class EquipmentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        $equipmentId = $this->route('id');

        return [
            'placa' => [
                'required', 'string', 'max:20', 'min:3',
                Rule::unique('equipment', 'placa')->ignore($equipmentId),
            ],
            'serial_niv' => ['nullable', 'string', 'max:90', 'min:3'],
            'body_serial_number' => ['nullable', 'string', 'max:90', 'min:3'],
            'chassis_serial_number' => ['nullable', 'string', 'max:90', 'min:3'],
            'engine_serial_number' => ['nullable', 'string', 'max:90', 'min:3'],
            'vehicle_model' => ['required', 'string', 'max:90', 'min:3'],
            'type' => ['required', 'exists:equipment_types,name'],
            'brand_name' => ['nullable', 'string', 'max:90', 'min:3'],
            'owner' => ['nullable', 'string', 'max:20', 'min:3'],
            'internal_code' => ['nullable', 'string', 'max:20', 'min:3'],
            'color' => ['nullable', 'string', 'max:20', 'min:3'],
            'origin' => ['nullable', 'string', 'max:255', 'min:3'],
            'model_year' => ['nullable', 'string'],
            'racda' => ['nullable', 'string', 'max:10'],
            'active' => ['sometimes', 'boolean'],
            'project_id' => ['nullable'],
            'project_id.*' => ['integer', 'exists:projects,id'],
        ];
    }
}
