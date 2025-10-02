<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Database\Seeders\PermissionsSeeder;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{

    public function run(): void
    {
        $this->superAdmin();
        $this->admin();
        $this->supervisor();
        $this->operador();
    }

    public function superAdmin()
    {
        $permissions = [];
        $role = Role::where('name', 'Super Admin')->first();

        foreach (PermissionsSeeder::PERMISSIONS_SUPER_ADMIN as $permission) {
            $per = Permission::where('name', $permission)->first();
            $permissions[] = $per->id;
        }

        $role->permissions()->sync($permissions);
    }

    public function admin()
    {
        $permissions = [];
        $role = Role::where('name', 'Admin')->first();

        foreach (PermissionsSeeder::PERMISSIONS_ADMIN as $permission) {
            $per = Permission::where('name', $permission)->first();
            $permissions[] = $per->id;
        }

        $role->permissions()->sync($permissions);
    }

    public function supervisor()
    {
        $permissions = [];
        $role = Role::where('name', 'Supervisor')->first();

        foreach (PermissionsSeeder::PERMISSIONS_SUPERVISOR as $permission) {
            $per = Permission::where('name', $permission)->first();
            $permissions[] = $per->id;
        }

        $role->permissions()->sync($permissions);
    }

    public function operador()
    {
        $permissions = [];
        $role = Role::where('name', 'Operador')->first();

        foreach (PermissionsSeeder::PERMISSIONS_OPERADOR as $permission) {
            $per = Permission::where('name', $permission)->first();
            $permissions[] = $per->id;
        }

        $role->permissions()->sync($permissions);
    }
}
