<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeeIncidentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $incidents)
    {
    }

    public function collection(): Collection
    {
        return $this->incidents;
    }

    public function headings(): array
    {
        return ['Cédula', 'Nombre', 'Apellido', 'Incidencia', 'Fecha', 'Reportado por'];
    }

    public function map($incident): array
    {
        return [
            $incident->employee?->identification_number,
            $incident->employee?->first_name,
            $incident->employee?->last_name,
            $incident->description,
            \Carbon\Carbon::parse($incident->date)->format('d-m-Y'),
            $incident->reportedBy?->name,
        ];
    }
}
