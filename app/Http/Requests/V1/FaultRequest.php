<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FaultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Normalmente se verifica si el usuario tiene permiso.
     */
    public function authorize(): bool
    {
        // Se establece a true por defecto, asumiendo que la autenticación
        // y la autorización de rutas se manejan en el middleware.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Obtener el ID de la falla de la ruta si estamos actualizando.
        // Asume que el parámetro de la ruta resource es 'fault'.
        $faultId = $this->route('fault');

        return [
            // Los campos de ID relacionales (branch_id) se excluyen de la validación
            // ya que se asignan en el controlador (session('branch')->id) y no vienen del request.

            'internal_id' => [
                'nullable', // Es nullable en la migración
                'string',
                'max:255',
                // Asegura la unicidad, pero ignora el registro actual si se está actualizando
                Rule::unique('faults', 'internal_id')->ignore($faultId),
            ],

            'employee_reported_id' => ['nullable', 'integer', 'exists:employees,id'],
            'equipment_id' => ['nullable', 'integer', 'exists:equipment,id'],
            'service_area_id' => ['nullable', 'integer', 'exists:service_areas,id'],
            'description' => ['nullable', 'string'],

            // Estatus y Ejecución (Todos nullable en la migración)
            'fault_status_id' => ['nullable', 'integer', 'exists:fault_statuses,id'],
            'spare_part_status_id' => ['nullable', 'integer', 'exists:spare_part_statuses,id'],

            // Las fechas son nullable y deben ser un formato de fecha válido
            'report_date' => ['nullable', 'date'],
            'scheduled_execution' => ['nullable', 'date'],
            'completed_execution' => ['nullable', 'date'],

            'executor_id' => ['nullable', 'integer', 'exists:employees,id'],
            'equipment_maintenance_log' => ['nullable', 'string'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Internal ID
            'internal_id.unique' => 'El identificador interno ya existe. Debe ser único.',
            'internal_id.string' => 'El identificador interno debe ser texto.',
            'internal_id.max' => 'El identificador interno no debe exceder :max caracteres.',

            // Relaciones (Foreign Keys)
            'employee_reported_id.integer' => 'El ID del empleado que reporta debe ser un número entero.',
            'employee_reported_id.exists' => 'El empleado que reporta seleccionado no es válido.',

            'equipment_id.integer' => 'El ID del equipo debe ser un número entero.',
            'equipment_id.exists' => 'El equipo seleccionado no es válido.',

            'service_area_id.integer' => 'El ID del área de servicio debe ser un número entero.',
            'service_area_id.exists' => 'El área de servicio seleccionada no es válida.',

            'fault_status_id.integer' => 'El ID del estatus de la falla debe ser un número entero.',
            'fault_status_id.exists' => 'El estatus de la falla seleccionado no es válido.',

            'spare_part_status_id.integer' => 'El ID del estatus del repuesto debe ser un número entero.',
            'spare_part_status_id.exists' => 'El estatus del repuesto seleccionado no es válido.',

            'executor_id.integer' => 'El ID del ejecutor debe ser un número entero.',
            'executor_id.exists' => 'El ejecutor seleccionado no es válido.',

            // Fechas
            'report_date.date' => 'La fecha de reporte debe ser una fecha válida.',
            'scheduled_execution.date' => 'La fecha de ejecución planificada debe ser una fecha válida.',
            'completed_execution.date' => 'La fecha de ejecución completada debe ser una fecha válida.',

            // Textos
            'description.string' => 'La descripción debe ser texto.',
            'equipment_maintenance_log.string' => 'El registro de mantenimiento debe ser texto.',
        ];
    }
}
