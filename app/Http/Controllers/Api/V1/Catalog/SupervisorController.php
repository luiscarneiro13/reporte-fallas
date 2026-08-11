<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\V1\Catalog\Concerns\RoleUserApiController;

class SupervisorController extends RoleUserApiController
{
    protected string $roleName = 'Supervisor';
    protected string $permissionBase = 'Supervisores';
    protected string $resourceName = 'Supervisor';
}
