<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class BranchHelper
{
    /**
     * Resuelve el branch_id de la sucursal del usuario autenticado vía Sanctum.
     *
     * A diferencia del flujo web (que depende de session('branch')), la API es
     * stateless: cada request resuelve la sucursal a partir de la relación
     * user->branches() (tabla user_branch), tomando la primera asociada.
     */
    public static function getBranchId(): ?int
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        return $user->branches()->first()?->id;
    }
}
