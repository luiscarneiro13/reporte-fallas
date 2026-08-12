<?php

namespace App\Services;

use App\Models\Equipment;
use Illuminate\Support\Str;

/**
 * Centraliza la asignación de uuid a un Equipment. La usan tanto
 * EquipmentObserver (creating/updating) como el endpoint de backfill, para no
 * duplicar en dos lugares la regla "solo generar si está vacío".
 */
class EquipmentUuidService
{
    /**
     * Genera el uuid si falta. Devuelve true si lo generó (false si ya tenía).
     */
    public function ensureUuid(Equipment $equipment): bool
    {
        if (!empty($equipment->uuid)) {
            return false;
        }

        $equipment->uuid = (string) Str::uuid();

        return true;
    }
}
