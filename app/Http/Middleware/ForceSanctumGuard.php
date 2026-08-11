<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Los roles/permisos de Spatie se siembran con guard_name='sanctum'
 * (ver app/Helpers/Permisos.php + seeders), pero config('auth.defaults.guard')
 * es 'web'. Sin esto, cualquier llamado a hasRole()/hasPermissionTo()/
 * getAllPermissions() sin guard explícito resuelve contra 'web' y no
 * encuentra nada (bug documentado en docs/api-endpoints-spec.md §2).
 *
 * Se aplica primero en el grupo /api/v1 (incluso antes de auth:sanctum, para
 * que también funcione en el propio login al calcular las abilities del token).
 */
class ForceSanctumGuard
{
    public function handle(Request $request, Closure $next)
    {
        Auth::shouldUse('sanctum');

        return $next($request);
    }
}
