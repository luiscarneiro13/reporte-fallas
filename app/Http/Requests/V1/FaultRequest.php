<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon; // Mantenemos Carbon para obtener la fecha

class FaultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Preprocesa los datos antes de aplicar las reglas de validación.
     */
    protected function prepareForValidation(): void
    {
        // 1. Manejo del ID del Ejecutor (mantener tu lógica)
        if ($this->has('executor_id') && $this->input('executor_id') === '0') {
            $this->merge([
                'executor_id' => 0,
            ]);
        }

        // 2. ⭐ Lógica para el campo de FECHA de cierre (closed)
        // Si el campo 'closed' existe (bandera enviada como "true"),
        // lo reemplazamos con la FECHA actual en formato 'Y-m-d'.
        if ($this->has('closed')) {
            $this->merge([
                // Usamos toDateString() o format('Y-m-d') para obtener solo la fecha
                'closed' => Carbon::now()->toDateString(),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $faultId = (int) $this->id;

        // 1. Reglas base
        $rules = [
            'internal_id' => [
                'nullable', 'string', 'max:255',
                Rule::unique('faults', 'internal_id')->ignore($faultId),
            ],

            // ⭐ Regla para el campo de fecha 'closed'
            'closed' => ['nullable', 'date'], // 'date' valida tanto 'date' como 'datetime', lo cual es suficiente

            // Campos de Asignación/Reporte
            'employee_reported_id' => ['required', 'integer', 'exists:employees,id'],
            'equipment_id' => ['required', 'integer', 'exists:equipment,id'],
            'service_area_id' => ['required', 'integer', 'exists:service_areas,id'],

            'description' => ['required', 'string'],

            // Estados
            'fault_status_id' => ['required', 'integer', 'exists:fault_statuses,id'],
            'spare_part_status_id' => ['required', 'integer', 'exists:spare_part_statuses,id'],

            // Fechas
            'report_date' => ['nullable', 'date'],
            'scheduled_execution' => ['nullable', 'date'],
            'completed_execution' => ['nullable', 'date'],

            // Ejecutor y Log
            'executor_id' => [
                'nullable', 'integer',
                function ($attribute, $value, $fail) {
                    if ($value !== 0 && !\DB::table('employees')->where('id', $value)->exists()) {
                        $fail('El ejecutor seleccionado no es válido.');
                    }
                },
            ],
            'equipment_maintenance_log' => ['nullable', 'string'],
        ];

        // 2. Lógica Condicional para el CIERRE
        if ($this->has('closed')) {
            // Requerir campos clave
            $requiredRules = [
                'employee_reported_id' => ['required', 'integer', 'exists:employees,id'],
                'equipment_id' => ['required', 'integer', 'exists:equipment,id'],
                'service_area_id' => ['required', 'integer', 'exists:service_areas,id'],

                'fault_status_id' => ['required', 'integer', 'exists:fault_statuses,id'],
                'spare_part_status_id' => ['required', 'integer', 'exists:spare_part_statuses,id'],

                'report_date' => ['required', 'date'],
                'scheduled_execution' => ['required', 'date'],
                'completed_execution' => ['required', 'date'],

                'executor_id' => [
                    'required', 'integer', 'min:1',
                ],
                'equipment_maintenance_log' => ['required', 'string'],
            ];

            $rules = array_merge($rules, $requiredRules);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        // Las reglas de validación en modo 'required'
        $requiredMessages = [
            'employee_reported_id.required' => 'El campo Reportado por es obligatorio al cerrar la falla.',
            'equipment_id.required' => 'El campo Equipo es obligatorio al cerrar la falla.',
            'service_area_id.required' => 'El campo Área de servicio es obligatorio al cerrar la falla.',
            'description.required' => 'La descripción es obligatoria al cerrar la falla.',

            'fault_status_id.required' => 'El Estatus de la falla es obligatorio al cerrar la falla.',
            'spare_part_status_id.required' => 'El Estatus de repuestos es obligatorio al cerrar la falla.',

            'report_date.required' => 'La Fecha del reporte es obligatoria al cerrar la falla.',
            'scheduled_execution.required' => 'La Ejecución planificada es obligatoria al cerrar la falla.',
            'completed_execution.required' => 'La Ejecución completada es obligatoria al cerrar la falla.',

            'executor_id.required' => 'El campo Actividad realizada por es obligatorio al cerrar la falla.',
            'executor_id.min' => 'Debe seleccionar un ejecutor válido al cerrar la falla.',

            'equipment_maintenance_log.required' => 'El registro de mantenimiento es obligatorio al cerrar la falla.',
        ];


        // Mensajes base
        $baseMessages = $this->getBaseMessages();

        $baseMessages['closed.date'] = 'El campo de cierre debe ser una fecha válida.';

        return array_merge($baseMessages, $requiredMessages);
    }

    protected function getBaseMessages(): array
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
