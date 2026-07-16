<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EquipmentExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $equipment)
    {
    }

    public function collection(): Collection
    {
        return $this->equipment;
    }

    public function headings(): array
    {
        return ['ID', 'Código interno', 'Tipo', 'Placa', 'Marca', 'Modelo', 'Año', 'Color', 'Proyecto'];
    }

    public function map($item): array
    {
        return [
            str_pad($item->id, 5, '0', STR_PAD_LEFT),
            $item->internal_code,
            $item->type,
            $item->placa,
            $item->brand_name,
            $item->vehicle_model,
            $item->model_year,
            $item->color,
            $item->lastProject?->name,
        ];
    }
}
