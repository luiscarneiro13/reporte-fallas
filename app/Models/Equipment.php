<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Equipment extends Model
{
    use HasFactory;

    protected $table = "equipment";

    protected $fillable = [
        'branch_id',
        'division_id',
        'project_id',
        'internal_code',
        'owner',
        'placa',
        'serial_niv',
        'body_serial_number',
        'chassis_serial_number',
        'engine_serial_number',
        'vehicle_model',
        'brand_name',
        'model_year',
        'color',
        'origin',
        'racda'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // Definición de la relación Muchos a Muchos (Usando 'projects' por convención)
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    /**
     * Define una relación HasOne que apunta a la tabla pivote,
     * ordenada para obtener la última asignación.
     * * Esta relación es el PASO INTERMEDIO ordenado:
     * Su único objetivo es encontrar la FILA MÁS RECIENTE de la tabla pivote 'equipment_project'
     * (basada en 'created_at') y obtener el 'project_id' de esa fila.
     * NO trae los datos completos del Project, sino solo la información del vínculo.
     */
    public function lastAssignmentPivot(): HasOne
    {
        // Apuntamos al modelo Pivote (EquipmentProject) para encontrar la fila más reciente
        return $this->hasOne(EquipmentProject::class)
            ->latest('equipment_project.created_at');
    }

    /**
     * Define el último proyecto asignado, usando la información ordenada del pivote.
     * Esta relación (HasOneThrough) utiliza la fila identificada por lastAssignmentPivot()
     * para saltar y cargar los datos completos del modelo Project.
     */
    public function lastProject(): HasOneThrough
    {
        return $this->hasOneThrough(
            Project::class, // El modelo final que queremos
            EquipmentProject::class, // El modelo intermedio (pivote)
            'equipment_id', // Foreign key en el modelo intermedio
            'id', // Foreign key en el modelo final (Project)
            'id', // Local key en Equipment
            'project_id' // Local key en el modelo intermedio que apunta a Project
        )->latest('equipment_project.created_at'); // Ordenamos para asegurar el último
    }

    public function faults()
    {
        return $this->belongsTo(Fault::class);
    }
}
