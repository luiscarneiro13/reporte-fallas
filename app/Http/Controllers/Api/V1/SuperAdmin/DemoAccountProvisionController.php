<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\DemoProvisioningService;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use RuntimeException;

/**
 * Crea las sucursales y cuentas ficticias para las revisiones de App Store y
 * Google Play. Procedimiento de uso en docs/demo-accounts.md.
 *
 * Va por HTTP y no por seeder porque el hosting es compartido, sin acceso a
 * artisan/CLI (igual que EquipmentUuidBackfillController).
 *
 * PROTECCIÓN: token obligatorio (DEMO_PROVISION_TOKEN en .env) comparado con
 * hash_equals. A diferencia del endpoint de backfill —que es inofensivo porque
 * solo rellena uuids nulos— este crea usuarios con credenciales conocidas sobre
 * la base de producción, así que sin token configurado responde 503 y no se
 * ejecuta. Es POST para que no pueda dispararse desde la barra del navegador ni
 * quedar cacheado.
 */
class DemoAccountProvisionController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request, DemoProvisioningService $service)
    {
        $expected = config('demo.provision_token');

        if (empty($expected)) {
            return $this->error(
                'Endpoint deshabilitado: falta configurar DEMO_PROVISION_TOKEN en el servidor.',
                503,
                null,
                'PROVISION_DISABLED'
            );
        }

        // Se lee del header o del CUERPO (post()), nunca de la query string:
        // `input()` también mira la URL y los access logs del hosting compartido
        // guardarían el token en texto plano.
        $provided = (string) ($request->header('X-Demo-Provision-Token') ?? '');

        if ($provided === '') {
            $fromBody = $request->post('token');
            $provided = is_string($fromBody) ? $fromBody : '';
        }

        if (!hash_equals((string) $expected, $provided)) {
            Log::warning('DemoAccountProvision: intento con token inválido', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $this->error('Token de provisioning inválido.', 401, null, 'INVALID_PROVISION_TOKEN');
        }

        $validated = $request->validate([
            // min:12 porque los emails de estas cuentas son públicos (están en
            // el repositorio) y viven en producción: la contraseña es la única
            // barrera.
            'password' => ['nullable', 'string', 'min:12', 'max:64'],
            'reset_passwords' => ['nullable', 'boolean'],
            'only' => ['nullable', 'array'],
            'only.*' => ['string', Rule::in(array_keys(DemoProvisioningService::BRANCHES))],
        ]);

        try {
            $result = $service->provision(
                $validated['password'] ?? null,
                (bool) ($validated['reset_passwords'] ?? false),
                $validated['only'] ?? []
            );
        } catch (RuntimeException $e) {
            // Errores de salvaguarda (sucursal de producción, usuario ajeno,
            // roles faltantes): son condiciones esperadas, no fallos del sistema.
            Log::warning('DemoAccountProvision: abortado por salvaguarda', ['message' => $e->getMessage()]);

            return $this->error($e->getMessage(), 409, null, 'PROVISION_ABORTED');
        } catch (\Throwable $e) {
            report($e);

            // Mensaje genérico a propósito: getMessage() de una QueryException
            // incluiría la consulta SQL y sus bindings. El detalle queda en los
            // logs del servidor vía report().
            return $this->error(
                'Error al provisionar las cuentas demo. Revisar storage/logs para el detalle.',
                500
            );
        }

        // Verificación de que ningún dropdown de "crear falla" quedó vacío.
        foreach ($result as $key => $data) {
            $branch = Branch::find($data['branch']['id']);
            $dropdowns = $service->dropdownReport($branch);

            $result[$key]['dropdowns'] = $dropdowns;
            $result[$key]['dropdowns_ok'] = !in_array(0, $dropdowns, true);
            $result[$key]['mail_enabled'] = in_array(
                (int) $branch->id,
                array_map('intval', config('demo.mail_enabled_branch_ids', [1])),
                true
            );
        }

        $allOk = collect($result)->every(fn ($data) => $data['dropdowns_ok'] && $data['mail_enabled'] === false);

        Log::info('DemoAccountProvision: ejecutado', [
            'ip' => $request->ip(),
            'branches' => collect($result)->pluck('branch.id')->all(),
            'all_ok' => $allOk,
        ]);

        return $this->success(
            ['branches' => $result, 'all_ok' => $allOk],
            $allOk
                ? 'Cuentas demo provisionadas correctamente.'
                : 'Cuentas demo provisionadas con advertencias: revisar dropdowns_ok y mail_enabled.'
        );
    }
}
