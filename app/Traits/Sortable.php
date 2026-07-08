<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait Sortable
{
    /**
     * Aplica ordenamiento dinámico a una consulta a partir de los parámetros
     * `sort_by` y `sort_dir` de la request, validándolos contra una lista blanca
     * de columnas permitidas (para evitar inyección SQL vía nombre de columna).
     *
     * @return array{0: string, 1: string} [$sortBy, $sortDir] efectivamente aplicados
     */
    public function applySort($query, Request $request, array $allowedSorts, string $defaultSort, string $defaultDir = 'desc'): array
    {
        $sortBy = $request->query('sort_by');
        $sortDir = strtolower((string) $request->query('sort_dir'));

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = $defaultSort;
        }

        if (!in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = $defaultDir;
        }

        $query->orderBy($sortBy, $sortDir);

        return [$sortBy, $sortDir];
    }
}
