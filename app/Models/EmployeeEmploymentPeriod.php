<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEmploymentPeriod extends Model
{
    use HasFactory;

    protected $table = "employee_employment_periods";

    protected $fillable = [
        'employee_id',
        'branch_id',
        'contract_type_id',
        'cargo_id',
        'start_date',
        'end_date',
        'termination_reason',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

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
}
