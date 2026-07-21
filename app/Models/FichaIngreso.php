<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaIngreso extends Model
{
    use HasFactory;

    protected $table = "ficha_ingreso";

    protected $fillable = [
        'employee_id',
        'photo',
        'birth_date',
        'nationality',
        'has_driver_license',
        'driver_license_grade',
        'account_number',
        'account_type',
        'bank',
        'has_occupational_certificate',
        'shirt_size',
        'coverall_size',
        'shoe_size',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'has_driver_license' => 'boolean',
        'has_occupational_certificate' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
