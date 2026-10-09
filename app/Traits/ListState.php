<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait ListState
{
    /**
     * Persiste en sesión la URL completa de un listado (filtros, orden y página),
     * con una clave por sucursal para no cruzar estados al cambiar de branch.
     *
     * - ?clear_filters=1: borra el estado guardado y recarga el listado limpio
     *   (la redirección evita que el parámetro quede arrastrado en el paginador).
     * - Entrada sin parámetros (p. ej. desde el menú): si hay estado guardado,
     *   redirige a él para volver justo donde estaba el usuario.
     *
     * Devuelve un RedirectResponse cuando hay que redirigir, o null para seguir.
     */
    private function restoreListState(Request $request, string $name, string $indexRoute): ?RedirectResponse
    {
        $branchId = session('branch')->id ?? 1;
        $stateKey = "list_state.{$name}.{$branchId}";

        if ($request->has('clear_filters')) {
            session()->forget($stateKey);
            return redirect()->route($indexRoute);
        }

        if (count($request->query()) === 0) {
            $savedUrl = session($stateKey);
            if ($savedUrl && is_string($savedUrl) && str_starts_with($savedUrl, url('/'))) {
                return redirect($savedUrl);
            }
            return null;
        }

        session([$stateKey => $request->fullUrl()]);

        return null;
    }
}
