<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FaultsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $faults)
    {
    }

    public function collection(): Collection
    {
        return $this->faults;
    }

    public function headings(): array
    {
        return ['ID', 'Código interno', 'Equipo', 'Descripción', 'Status de falla', 'Status de repuesto', 'Area de servicio', 'Tiempo en espera'];
    }

    public function map($fault): array
    {
        return [
            str_pad($fault->id, 5, '0', STR_PAD_LEFT),
            $fault->internal_code,
            $fault->equipment_name,
            $fault->description,
            $fault->fault_status_name,
            $fault->spare_part_status_name,
            $fault->service_area_name,
            $fault->duration_days ? $fault->duration_days . ' dias' : 'Hoy',
        ];
    }
}
