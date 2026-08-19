<?php

namespace App\Services;

use App\Models\EquipmentType;
use App\Models\Project;

/**
 * Catálogos para las pantallas de Equipos (index/filtro, crear, editar).
 * $branchId es opcional (default: session('branch')->id) para que la web siga
 * funcionando sin cambios y la API (stateless) pueda pasarlo explícito vía
 * BranchHelper::getBranchId() — mismo patrón que FaultService.
 */
class EquipmentService
{
    /**
     * Proyectos para el filtro del listado (V1\AdminBranch\EquipmentController::index).
     * Ordenado por nombre.
     */
    static function projectsForFilter($branchId = null)
    {
        $branchId ??= session('branch')->id;

        return Project::where('branch_id', $branchId)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    /**
     * Proyectos para el select del formulario crear/editar. Sin orden (así
     * está en la web original — no se "corrige" acá para no divergir).
     */
    static function projectsForForm($branchId = null)
    {
        $branchId ??= session('branch')->id;

        return Project::where('branch_id', $branchId)->pluck('name', 'id');
    }

    /**
     * Tipos de equipo, plucked por 'name' tanto en key como en value: el
     * campo `equipment.type` guarda el nombre del tipo directamente, no un id
     * (ver EquipmentRequest: 'type' => 'exists:equipment_types,name').
     */
    static function equipmentTypes($branchId = null)
    {
        $branchId ??= session('branch')->id;

        return EquipmentType::where('branch_id', $branchId)->pluck('name', 'name');
    }

    /**
     * Rango de años para el select "Año", más reciente primero. No depende
     * de branch_id ni de sesión.
     */
    static function modelYears(int $startYear = 1940): array
    {
        $endYear = date('Y');

        $modelYears = collect(range($startYear, $endYear))
            ->reverse()
            ->mapWithKeys(fn ($year) => [(string) $year => (string) $year]);

        $modelYears->prepend('N/A', '');

        return $modelYears->all();
    }
}
