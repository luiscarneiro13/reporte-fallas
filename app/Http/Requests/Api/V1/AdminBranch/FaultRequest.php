<?php

namespace App\Http\Requests\Api\V1\AdminBranch;

use App\Helpers\BranchHelper;
use App\Http\Requests\Api\V1\ApiFormRequest;
use App\Models\Branch;
use App\Models\FaultStatus;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

/**
 * Usado tanto en store() como en update() (spec §6.2.7). Adaptado del
 * FormRequest web equivalente, reemplazando la dependencia de
 * session('branch') por BranchHelper (la API es stateless).
 */
class FaultRequest extends ApiFormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('executor_id') && $this->input('executor_id') === '0') {
            $this->merge(['executor_id' => 0]);
        }

        if ($this->has('executor_external_id') && $this->input('executor_external_id') === '0') {
            $this->merge(['executor_external_id' => 0]);
        }

        // El payload trae la clave 'closed' como bandera de cierre: se reemplaza
        // por la fecha actual del servidor, el cliente no controla la fecha de cierre.
        if ($this->has('closed')) {
            $this->merge(['closed' => Carbon::now()->toDateString()]);
        }

        if (!$this->has('closed') && $this->isOperator()) {
            $employeeId = $this->user()?->employees()->first()?->id;

            if ($employeeId && !$this->filled('employee_reported_id')) {
                $this->merge(['employee_reported_id' => $employeeId]);
            }

            if (!$this->filled('fault_status_id')) {
                $this->merge(['fault_status_id' => $this->operatorFaultStatusId()]);
            }
        }
    }

    private function isOperator(): bool
    {
        $user = $this->user();

        return $user && $user->hasRole('Operador', 'sanctum');
    }

    private function operatorFaultStatusId(): ?int
    {
        $branch = Branch::find(BranchHelper::getBranchId());

        if (!$branch) {
            return null;
        }

        return $branch->faultStatus()
            ->firstOrCreate(['name' => FaultStatus::OPERATOR_STATUS_NAME])
            ->id;
    }

    public function rules(): array
    {
        $faultId = $this->route('id');

        $rules = [
            'internal_id' => [
                'nullable', 'string', 'max:255',
                Rule::unique('faults', 'internal_id')->ignore($faultId),
            ],
            'closed' => ['nullable', 'date'],
            'employee_reported_id' => ['required', 'integer', 'exists:employees,id'],
            'equipment_id' => ['required', 'integer', 'exists:equipment,id'],
            'service_area_id' => ['required', 'integer', 'exists:service_areas,id'],
            'description' => ['required', 'string'],
            'fault_status_id' => ['required', 'integer', 'exists:fault_statuses,id'],
            'spare_part_status_id' => ['required', 'integer', 'exists:spare_part_statuses,id'],
            'report_date' => ['nullable', 'date'],
            'scheduled_execution' => ['nullable', 'date'],
            'completed_execution' => ['nullable', 'date'],
            'executor_id' => ['nullable', 'integer'],
            'executor_external_id' => ['nullable', 'integer'],
            'equipment_maintenance_log' => ['nullable', 'string'],
        ];

        if ($this->has('closed')) {
            // executor_id/executor_external_id quedan nullable también al cerrar
            // (igual que el FormRequest web): son alternativas entre sí — un cierre
            // válido puede traer solo ejecutor externo, sin ejecutor interno.
            $rules = array_merge($rules, [
                'employee_reported_id' => ['required', 'integer', 'exists:employees,id'],
                'equipment_id' => ['required', 'integer', 'exists:equipment,id'],
                'service_area_id' => ['required', 'integer', 'exists:service_areas,id'],
                'fault_status_id' => ['required', 'integer', 'exists:fault_statuses,id'],
                'spare_part_status_id' => ['required', 'integer', 'exists:spare_part_statuses,id'],
                'report_date' => ['required', 'date'],
                'scheduled_execution' => ['required', 'date'],
                'completed_execution' => ['required', 'date'],
                'equipment_maintenance_log' => ['required', 'string'],
            ]);
        }

        return $rules;
    }
}
