<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fault extends Model
{
    use HasFactory;

    protected $table = "faults";

    protected $fillable = [
        'branch_id', // Sucursal
        'internal_id', // Id interno de la falla
        'employee_reported_id', // Id del empleado que reportó la falla
        'equipment_id', // Equipo asociado
        'service_area_id', // Area de servicio
        'description',

        'fault_status_id', // Estatus de la falla
        'spare_part_status_id', // Estatus del repuesto
        'report_date', // Fecha del reporte
        'scheduled_execution', // Ejecución planificada
        'completed_execution', // Ejecución completada
        'executor_id', // Actividad realizada por

        'equipment_maintenance_log' // Actividades realizadas al equipo

    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function serviceArea()
    {
        return $this->belongsTo(ServiceArea::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function faultStatus()
    {
        return $this->belongsTo(FaultStatus::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(Employee::class, 'employee_reported_id');
    }

    public function sparePartStatus()
    {
        return $this->belongsTo(SparePartStatus::class);
    }

    public function executor()
    {
        return $this->belongsTo(Employee::class, 'executor_id');
    }
}
