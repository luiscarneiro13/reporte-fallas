<?php

namespace App\Services;

use App\Mail\CerrarFallaEmail;
use App\Mail\ReportarFallaEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Único punto de envío de los correos de fallas (EMAIL_FALLAS). Existía
 * duplicado en dos controladores (API móvil y web) con el mismo Mail::to(...)
 * copiado; se centralizó acá para que la regla de "qué sucursales envían
 * correo" no pueda quedar aplicada en uno y olvidada en el otro.
 *
 * La regla (config/demo.php → mail_enabled_branch_ids) es una lista blanca:
 * solo las sucursales reales envían correo. Así las sucursales demo que usan
 * los revisores de App Store / Google Play pueden crear y cerrar fallas sin
 * escribirle al buzón de mantenimiento de la empresa.
 *
 * Nunca lanza excepciones: un fallo de correo no debe romper la creación o el
 * cierre de una falla (mismo criterio que PushNotificationService).
 */
class FaultMailService
{
    public function sendReported($faultView, ?int $branchId): bool
    {
        return $this->send(
            fn ($recipient) => Mail::to($recipient)->send(new ReportarFallaEmail($faultView)),
            $branchId,
            'fault_reported'
        );
    }

    public function sendClosed($historyRecord, ?int $branchId): bool
    {
        return $this->send(
            fn ($recipient) => Mail::to($recipient)->send(new CerrarFallaEmail($historyRecord)),
            $branchId,
            'fault_closed'
        );
    }

    /**
     * Indica si una sucursal está autorizada a enviar correos de fallas.
     * `null` (usuario sin sucursal resuelta) cuenta como no autorizada.
     */
    public function isMailEnabledForBranch(?int $branchId): bool
    {
        if ($branchId === null) {
            return false;
        }

        return in_array($branchId, config('demo.mail_enabled_branch_ids', [1]), true);
    }

    private function send(callable $sender, ?int $branchId, string $event): bool
    {
        if (!$this->isMailEnabledForBranch($branchId)) {
            Log::info('FaultMailService: correo omitido por sucursal no habilitada', [
                'event' => $event,
                'branch_id' => $branchId,
            ]);

            return false;
        }

        // Se lee solo de config (config/mail.php ya toma EMAIL_FALLAS del .env):
        // un fallback a env() acá sería redundante y devolvería null si alguna
        // vez se ejecuta `config:cache`.
        $recipient = config('mail.fault_notifications_to');

        if (empty($recipient)) {
            Log::warning('FaultMailService: EMAIL_FALLAS sin configurar, correo omitido', [
                'event' => $event,
                'branch_id' => $branchId,
            ]);

            return false;
        }

        try {
            $sender($recipient);

            return true;
        } catch (\Throwable $th) {
            report($th);

            return false;
        }
    }
}
