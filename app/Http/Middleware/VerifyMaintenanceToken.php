<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Protege endpoints de mantenimiento de un solo uso (ej. backfill de uuid de
 * Equipment) con un secreto extra en .env, además de auth+rol: son acciones
 * operativas que no deberían quedar disparables por cualquier Super Admin
 * que simplemente descubra la ruta.
 */
class VerifyMaintenanceToken
{
    public function handle(Request $request, Closure $next)
    {
        $expected = config('services.maintenance.token');
        $provided = $request->header('X-Maintenance-Token') ?? $request->query('token');

        if (!$expected || !$provided || !hash_equals((string) $expected, (string) $provided)) {
            abort(403, 'Token de mantenimiento inválido o no configurado.');
        }

        return $next($request);
    }
}
