<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeDataExport implements FromArray, WithHeadings
{
    public function __construct(private Employee $employee)
    {
    }

    public function headings(): array
    {
        return [
            'Cédula',
            'Nombre',
            'Apellido',
            'Teléfono',
            'Dirección',
            'Ejecutor de servicio',
            'Cargo',
            'Proyecto',
            'Fecha de ingreso',
            'Tipo de contrato',
            'Fecha de nacimiento',
            'Nacionalidad',
            'Posee licencia de conducir',
            'Grado de licencia',
            'Número de cuenta',
            'Tipo de cuenta',
            'Banco',
            'Posee certificado ocupacional',
            'Talla de camisa',
            'Talla de braga',
            'Talla de calzado',
            'Contacto de emergencia',
            'Teléfono de contacto de emergencia',
            'Email de usuario',
            'Rol de sistema',
        ];
    }

    public function array(): array
    {
        $employee = $this->employee;
        $ficha = $employee->fichaIngreso;
        $userSystem = $employee->users->first();

        return [
            [
                $employee->identification_number,
                $employee->first_name,
                $employee->last_name,
                $employee->phone_number,
                $employee->address,
                $employee->executor ? 'Si' : 'No',
                $employee->cargo?->name,
                $employee->projects->first()?->name,
                $employee->hire_date,
                $employee->contractType?->name,
                $ficha?->birth_date,
                $ficha?->nationality,
                $ficha?->has_driver_license ? 'Si' : 'No',
                $ficha?->driver_license_grade,
                $ficha?->account_number,
                $ficha?->account_type,
                $ficha?->bank,
                $ficha?->has_occupational_certificate ? 'Si' : 'No',
                $ficha?->shirt_size,
                $ficha?->coverall_size,
                $ficha?->shoe_size,
                $ficha?->emergency_contact_name,
                $ficha?->emergency_contact_phone,
                $userSystem?->email,
                $userSystem?->roles->first()?->name,
            ],
        ];
    }
}
