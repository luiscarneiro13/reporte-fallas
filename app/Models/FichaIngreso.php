<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'driver_license_expiration_date',
        'account_number',
        'account_type',
        'bank',
        'has_occupational_certificate',
        'occupational_certificate_expiration_date',
        'shirt_size',
        'coverall_size',
        'shoe_size',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    // has_driver_license / has_occupational_certificate: 0 = No, 1 = Sí, 2 = N/A
    protected $casts = [
        'birth_date' => 'date',
        'driver_license_expiration_date' => 'date',
        'has_driver_license' => 'integer',
        'has_occupational_certificate' => 'integer',
        'occupational_certificate_expiration_date' => 'date',
    ];

    protected function hasDriverLicenseLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ((int) $this->has_driver_license) {
                1 => 'Si',
                2 => 'N/A',
                default => 'No',
            },
        );
    }

    protected function hasOccupationalCertificateLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ((int) $this->has_occupational_certificate) {
                1 => 'Si',
                2 => 'N/A',
                default => 'No',
            },
        );
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
