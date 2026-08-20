<?php

namespace App\Services;

use App\Models\Fault;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Capa de negocio: decide a quién y qué notificar para cada evento de dominio.
 * Delega el envío real a ExpoPushService (transporte). Nunca lanza excepciones:
 * un fallo de push no debe romper el flujo de creación/cierre de una falla.
 *
 * Replica el comportamiento de App\Services\PushNotificationService de ironflow
 * (mismos roles destinatarios, mismo texto de título/cuerpo, mismo campo `url`
 * de deep link). Diferencia deliberada en el `url`: ironflow arma el path con el
 * `uuid` del equipo (`/equipment/{uuid}`) porque ahí la API busca equipos por
 * uuid; acá GET /api/v1/equipos/{id} (que usa app-reporte-fallas en
 * src/api/equipment.js) solo busca por id numérico — no existe búsqueda por
 * uuid todavía (ver [[reportefallas_equipment_uuid]]). Por eso el path usa
 * `equipment_id`, no `equipment_uuid`, aunque el campo `equipment_uuid` del
 * payload se mantiene igual que en ironflow (metadata informativa).
 */
class PushNotificationService
{
    private const DEEP_LINK_BASE_URL = 'https://servicioscasmar.com';

    public function __construct(private ExpoPushService $expo)
    {
    }

    public function notifyNewFault(Fault $fault): void
    {
        try {
            $userIds = $this->getBranchAdminAndSupervisorIds($fault->branch_id);

            if (empty($userIds)) {
                return;
            }

            $result = $this->expo->sendToUsers($userIds, [
                'type' => 'fault_created',
                'fault_id' => (string) $fault->id,
                'equipment_uuid' => (string) $fault->equipment?->uuid,
                'url' => (string) $this->deepLinkUrl($fault),
                'title' => 'Nueva falla reportada',
                'body' => "Se reportó una falla en el equipo {$this->equipmentName($fault)}",
            ]);

            Log::info('PushNotificationService: fault_created', [
                'fault_id' => $fault->id,
                'recipients' => count($userIds),
                'sent' => $result['sent'],
                'failed' => $result['failed'],
                'invalid_removed' => $result['invalid_tokens_removed'],
            ]);
        } catch (\Throwable $e) {
            Log::error('PushNotificationService: notifyNewFault failed', [
                'fault_id' => $fault->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function notifyClosedFault(Fault $fault): void
    {
        try {
            $userIds = $this->getBranchAdminAndSupervisorIds($fault->branch_id);

            if ($fault->employee_reported_id) {
                $operatorUserIds = DB::table('employee_users')
                    ->where('employee_id', $fault->employee_reported_id)
                    ->pluck('user_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();

                $userIds = array_values(array_unique(array_merge($userIds, $operatorUserIds)));
            }

            if (empty($userIds)) {
                return;
            }

            $result = $this->expo->sendToUsers($userIds, [
                'type' => 'fault_closed',
                'fault_id' => (string) $fault->id,
                'equipment_uuid' => (string) $fault->equipment?->uuid,
                'url' => (string) $this->deepLinkUrl($fault),
                'title' => 'Falla cerrada',
                'body' => "La falla del equipo {$this->equipmentName($fault)} ha sido cerrada y archivada",
            ]);

            Log::info('PushNotificationService: fault_closed', [
                'fault_id' => $fault->id,
                'recipients' => count($userIds),
                'sent' => $result['sent'],
                'failed' => $result['failed'],
                'invalid_removed' => $result['invalid_tokens_removed'],
            ]);
        } catch (\Throwable $e) {
            Log::error('PushNotificationService: notifyClosedFault failed', [
                'fault_id' => $fault->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Path parseado por app-reporte-fallas (App.js: pathParts[0] === 'equipment').
     * Usa el id numérico (no el uuid, a diferencia de ironflow) porque
     * GET /api/v1/equipos/{id} busca por id — ver docblock de la clase.
     */
    private function deepLinkUrl(Fault $fault): ?string
    {
        if (!$fault->equipment_id) {
            return null;
        }

        return self::DEEP_LINK_BASE_URL . '/equipment/' . $fault->equipment_id;
    }

    private function equipmentName(Fault $fault): string
    {
        $equipment = $fault->equipment;

        return $equipment?->placa
            ?? $equipment?->internal_code
            ?? ('Equipo #' . $fault->equipment_id);
    }

    /**
     * Igual a ironflow: solo Admin/Supervisor de la sucursal de la falla
     * (guard `sanctum`, ver App\Http\Middleware\ForceSanctumGuard).
     */
    private function getBranchAdminAndSupervisorIds(int $branchId): array
    {
        return DB::table('users')
            ->join('user_branch', 'user_branch.user_id', '=', 'users.id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'users.id')
                    ->where('model_has_roles.model_type', \App\Models\User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('user_branch.branch_id', $branchId)
            ->where('roles.guard_name', 'sanctum')
            ->whereIn('roles.name', ['Admin', 'Supervisor'])
            ->distinct()
            ->pluck('users.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}
