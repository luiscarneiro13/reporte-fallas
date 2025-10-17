<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected $casts = [
        'employee_reported_id' => 'integer',
        'equipment_id' => 'integer',
        'service_area_id' => 'integer',
        'fault_status_id' => 'integer',
        'spare_part_status_id' => 'integer',
        'executor_id' => 'integer',
    ];

    protected $appends = ['days_since_report'];

    /**
     * Mutator: Convierte 0 o "0" a NULL para la base de datos en el campo executor_id.
     * Esto limpia el controlador.
     */
    protected function executorId(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => ($value == 0 || $value === '0') ? null : $value,
        );
    }

    protected function daysSinceReport(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                // Accedemos al valor crudo de la base de datos
                $rawReportDate = $attributes['report_date'] ?? null;

                if ($rawReportDate) {
                    // 🎯 FIX: Usamos Carbon::parse en el valor crudo para garantizar que es un objeto de fecha.
                    $carbonDate = Carbon::parse($rawReportDate);

                    // Usar diffForHumans para obtener el tiempo transcurrido en formato legible (ej: "hace 5 días")
                    return $carbonDate->diffForHumans(['syntax' => CarbonInterface::DIFF_RELATIVE_TO_NOW]);
                }
                return '';
            },
        );
    }

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
