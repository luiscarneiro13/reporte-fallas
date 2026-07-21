<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $employees)
    {
    }

    public function collection(): Collection
    {
        return $this->employees;
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

    public function map($employee): array
    {
        $ficha = $employee->fichaIngreso;
        $userSystem = $employee->users->first();

        return [
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
        ];
    }
}
