<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\V1\Catalog\Concerns\RoleUserApiController;

/**
 * Spec §6.3.7: el original usaba el permiso "Supervisores *" por bug de
 * copy-paste. Corregido a "Operadores *" (recomendación explícita de la spec).
 */
class OperatorController extends RoleUserApiController
{
    protected string $roleName = 'Operador';
    protected string $permissionBase = 'Operadores';
    protected string $resourceName = 'Operador';
}
