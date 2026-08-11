<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\V1\Catalog\Concerns\RoleUserApiController;

/**
 * Spec §6.3.9 menciona heredar language_id de la sucursal al crear — este
 * proyecto no tiene módulo de idiomas (sin tabla `languages`), así que ese
 * paso no aplica aquí.
 */
class AdministradoresController extends RoleUserApiController
{
    protected string $roleName = 'Admin';
    protected string $permissionBase = 'Administradores';
    protected string $resourceName = 'Administrador';
}
