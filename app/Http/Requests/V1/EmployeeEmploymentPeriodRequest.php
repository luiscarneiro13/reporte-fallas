<?php

namespace App\Http\Requests\V1;

use App\Models\EmployeeEmploymentPeriod;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeEmploymentPeriodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Los selects usan '0' como valor centinela para "sin seleccionar";
     * se normaliza a null para que la regla "exists" no lo rechace.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'contract_type_id' => $this->input('contract_type_id') ?: null,
            'cargo_id' => $this->input('cargo_id') ?: null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'contract_type_id' => 'nullable|exists:contract_types,id',
            'cargo_id' => 'nullable|exists:cargos,id',
            'termination_reason' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'start_date' => 'fecha de ingreso',
            'end_date' => 'fecha de egreso',
            'contract_type_id' => 'tipo de contrato',
            'cargo_id' => 'cargo',
            'termination_reason' => 'motivo de egreso',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled('end_date')) {
                return;
            }

            $currentPeriod = $this->route('periodos_empleado');
            $currentPeriodId = $currentPeriod instanceof EmployeeEmploymentPeriod ? $currentPeriod->id : $currentPeriod;

            $hasOpenPeriod = EmployeeEmploymentPeriod::where('employee_id', $this->input('employee_id'))
                ->whereNull('end_date')
                ->when($currentPeriodId, fn($q) => $q->where('id', '!=', $currentPeriodId))
                ->exists();

            if ($hasOpenPeriod) {
                $validator->errors()->add(
                    'end_date',
                    'Este empleado ya tiene un período activo (sin fecha de egreso). Debe finalizarlo antes de crear o dejar abierto otro.'
                );
            }
        });
    }
}
