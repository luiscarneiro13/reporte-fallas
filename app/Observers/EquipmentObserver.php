<?php

namespace App\Observers;

use App\Models\Equipment;
use App\Services\EquipmentUuidService;

/**
 * Asegura uuid tanto en creación como en edición (no solo "updated"): hay
 * equipos viejos sin uuid (columna agregada por migración a una tabla con
 * datos) que pueden guardarse sin tocar ese campo — por eso se cubre también
 * "updating", no solo "creating".
 */
class EquipmentObserver
{
    public function __construct(protected EquipmentUuidService $uuidService)
    {
    }

    public function creating(Equipment $equipment): void
    {
        $this->uuidService->ensureUuid($equipment);
    }

    public function updating(Equipment $equipment): void
    {
        $this->uuidService->ensureUuid($equipment);
    }
}
