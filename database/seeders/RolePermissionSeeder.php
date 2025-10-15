<?php

namespace Database\Seeders;

use App\Helpers\Permisos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Database\Seeders\PermissionsSeeder;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Solo llamamos al método unificado
        $this->assignPermissionsToRoles();
    }

    /**
     * Recorre el mapa de permisos, asegura que los permisos existan
     * y sincroniza los IDs de los permisos con los roles existentes.
     */
    protected function assignPermissionsToRoles(): void
    {
        $permissionsByRole = [];

        // 1. Iteración sobre el mapa de permisos
        foreach (Permisos::PERMISSIONS_MAP as $permissionName => $roleIndexes) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);

            // 2. Mapeo del índice numérico al nombre del rol
            foreach ($roleIndexes as $index) {
                $roleName = $this->getRoleNameByIndex($index);

                if ($roleName) {
                    if (!isset($permissionsByRole[$roleName])) {
                        $permissionsByRole[$roleName] = [];
                    }
                    // Usa el nombre del rol como clave
                    $permissionsByRole[$roleName][] = $permission->id;
                }
            }
        }

        // 3. Sincronizar los permisos
        foreach ($permissionsByRole as $roleName => $permissionIds) {
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                $role->permissions()->sync($permissionIds);
            }
        }
    }

    protected function getRoleNameByIndex(int $index): ?string
    {
        // Voltea el array para que los índices (0, 1, 2, 3) sean las claves
        $indexToNameMap = array_flip(Permisos::ROLES_MAP);

        // Retorna el nombre del rol si existe el índice, sino retorna null
        return $indexToNameMap[$index] ?? null;
    }
}
