<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Employee extends Model
{
    use HasFactory;

    protected $table = "employees";

    protected $fillable = ['branch_id', 'identification_number', 'first_name', 'last_name', 'phone_number', 'address', 'executor', 'external', 'position', 'cargo_id', 'hire_date', 'contract_type_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function contractType()
    {
        return $this->belongsTo(ContractType::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function fichaIngreso()
    {
        return $this->hasOne(FichaIngreso::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'employee_users')->withTimestamps();
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    public function reportedFaults()
    {
        return $this->hasMany(Fault::class, 'employee_reported_id');
    }

    public function incidents()
    {
        return $this->hasMany(EmployeeIncident::class);
    }

    public function employmentPeriods()
    {
        return $this->hasMany(EmployeeEmploymentPeriod::class)->orderByDesc('start_date');
    }

    public function currentEmploymentPeriod()
    {
        return $this->hasOne(EmployeeEmploymentPeriod::class)->whereNull('end_date')->latestOfMany('start_date');
    }

    public function isCurrentlyActive(): bool
    {
        return $this->employmentPeriods()->whereNull('end_date')->exists();
    }

    public function executorServiceAreas()
    {
        // Sintaxis:
        // HasManyThrough(Modelo_Final, Modelo_Intermedio, Llave_Foránea_en_Intermedio, Llave_Foránea_en_Final)

        return $this->hasManyThrough(
            ServiceArea::class, // Modelo final (el que quieres obtener)
            Fault::class,       // Modelo intermedio (el que tiene la llave foránea del Empleado y la de ServiceArea)
            'executor_id',      // Llave foránea en la tabla 'faults' que apunta a 'employees'
            'id',               // Llave foránea en la tabla 'faults' que apunta a 'service_areas' (usamos 'id' de ServiceArea)
            'id',               // Llave local en la tabla 'employees'
            'service_area_id'   // Llave local en la tabla 'faults' que apunta a 'service_areas'
        )
            ->distinct(); // Opcional: Para evitar duplicados si un empleado tiene varias fallas en la misma área
    }

    protected function email(): Attribute
    {
        // El 'set' se ejecuta justo antes de guardar o actualizar el modelo.
        return Attribute::make(
            set: fn(?string $value) => $value !== null ? strtolower($value) : null,
        );
    }
}
