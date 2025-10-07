<?php

namespace Database\Seeders;

use App\Helpers\Permisos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{

    public function run(): void
    {
        $this->permissionsAll();
    }

    // Se crean todos los permisos en la base de datos
    public function permissionsAll()
    {
        // Usamos $permissionName (clave) y $roleIndices (valor)
        foreach (Permisos::PERMISSIONS_MAP as $permissionName => $roleIndices) {

            // Usamos firstOrCreate() para evitar la lógica de verificación manual
            // Aseguramos que el guard_name sea 'sanctum' para que coincida con los roles.
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'sanctum']
            );
        }
    }
}
