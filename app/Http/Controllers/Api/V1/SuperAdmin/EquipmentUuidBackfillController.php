<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Services\EquipmentUuidService;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\Request;

/**
 * Backfill manual de uuid para Equipment. La columna se agregó nullable
 * porque la tabla ya tenía datos y este hosting no permite comandos artisan
 * (sin SSH/CLI) — se dispara a mano (curl/navegador), una o varias veces,
 * hasta que la respuesta diga remaining=0.
 *
 * SIN NINGUNA PROTECCIÓN (a pedido explícito): quien tenga la URL puede
 * ejecutarlo. Es tolerable porque es idempotente y solo toca filas con
 * uuid nulo (no puede pisar ni duplicar datos), pero por eso mismo es
 * crítico borrar esta ruta/controlador en cuanto remaining llegue a 0 y se
 * aplique la migración NOT NULL — no dejarla expuesta indefinidamente.
 */
class EquipmentUuidBackfillController extends Controller
{
    use ApiResponse;

    public function backfill(Request $request, EquipmentUuidService $uuidService)
    {
        $limit = max(1, min((int) $request->query('limit', 100), 500));

        $equipos = Equipment::whereNull('uuid')->orderBy('id')->limit($limit)->get();

        $processed = 0;
        foreach ($equipos as $equipment) {
            if ($uuidService->ensureUuid($equipment)) {
                $equipment->save();
                $processed++;
            }
        }

        $remaining = Equipment::whereNull('uuid')->count();

        return $this->success([
            'processed' => $processed,
            'remaining' => $remaining,
        ], 'Backfill de uuid ejecutado.');
    }
}
