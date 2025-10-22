<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaultHistory extends Model
{
    use HasFactory;

    // Asocia el modelo a la tabla histórica
    protected $table = 'fault_history';

    /**
     * Los atributos que son asignables masivamente (Mass Assignment).
     * Listados en el orden de las columnas de tu vista/migración de historial.
     */
    protected $fillable = [
        'original_fault_id', // Campo de referencia
        'branch_id',
        'description',

        // Fechas
        'report_date',
        'closed_at', // Mapeado desde 'closed' en el controlador
        'scheduled_execution',
        'completed_execution',

        // Textos y Códigos
        'internal_id',
        'equipment_maintenance_log',
        'internal_code', // Dato desnormalizado del equipo

        // IDs y Nombres del Reportante
        'reported_by_id', // ID del empleado que reporta
        'reported_by_name',

        // IDs y Nombres del Equipo
        'equipment_id',
        'equipment_name',

        // IDs y Nombres del Área de Servicio
        'service_area_id',
        'service_area_name',

        // IDs y Nombres del Estado de Falla
        'fault_status_id',
        'fault_status_name',

        // IDs y Nombres del Estado de Repuesto
        'spare_part_status_id',
        'spare_part_status_name',

        // IDs y Nombres del Ejecutor
        'executor_id',
        'executor_name',
    ];

    protected $casts = [
        'report_date' => 'date',
        'scheduled_execution' => 'date',
        'completed_execution' => 'date',
        'closed_at' => 'date',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }
}
