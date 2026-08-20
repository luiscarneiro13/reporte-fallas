<?php

namespace App\Services;

use App\Models\Fault;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Capa de negocio: decide a quién y qué notificar para cada evento de dominio.
 * Delega el envío real a ExpoPushService (transporte). Nunca lanza excepciones:
 * un fallo de push no debe romper el flujo de creación/cierre de una falla.
 */
class PushNotificationService
{
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

            $this->expo->sendToUsers($userIds, [
                'type' => 'fault_created',
                'title' => 'Nueva falla reportada',
                'body' => $this->faultSummary($fault),
                'fault_id' => $fault->id,
                'equipment_uuid' => $fault->equipment?->uuid,
            ]);
        } catch (\Throwable $e) {
            Log::error('PushNotificationService::notifyNewFault falló', [
                'fault_id' => $fault->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function notifyClosedFault(Fault $fault): void
    {
        try {
            $userIds = $this->getBranchAdminAndSupervisorIds($fault->branch_id);

            $reporterUserIds = $fault->reportedBy?->users()->pluck('users.id')->all() ?? [];
            $userIds = array_values(array_unique(array_merge($userIds, $reporterUserIds)));

            if (empty($userIds)) {
                return;
            }

            $this->expo->sendToUsers($userIds, [
                'type' => 'fault_closed',
                'title' => 'Falla cerrada',
                'body' => $this->faultSummary($fault),
                'fault_id' => $fault->id,
                'equipment_uuid' => $fault->equipment?->uuid,
            ]);
        } catch (\Throwable $e) {
            Log::error('PushNotificationService::notifyClosedFault falló', [
                'fault_id' => $fault->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function faultSummary(Fault $fault): string
    {
        $equipmentName = $fault->equipment?->full_equipment_name;

        return $equipmentName ? "Equipo: {$equipmentName}" : "Falla #{$fault->id}";
    }

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
            ->whereIn('roles.name', ['Super Admin', 'Admin', 'Supervisor'])
            ->distinct()
            ->pluck('users.id')
            ->all();
    }
}
