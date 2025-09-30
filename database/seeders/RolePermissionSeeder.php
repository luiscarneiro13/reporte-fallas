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
        $this->adminSucursal();
        $this->cajero();
        $this->vendedor();
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

    public function adminSucursal()
    {
        $permissions = [];
        $role = Role::where('name', 'Admin Sucursal')->first();

        foreach (PermissionsSeeder::PERMISSIONS_ADMIN_SUCURSAL as $permission) {
            $per = Permission::where('name', $permission)->first();
            $permissions[] = $per->id;
        }

        $role->permissions()->sync($permissions);
    }

    public function cajero()
    {
        $permissions = [];
        $role = Role::where('name', 'Cajero')->first();

        foreach (PermissionsSeeder::PERMISSIONS_CAJERO as $permission) {
            $per = Permission::where('name', $permission)->first();
            $permissions[] = $per->id;
        }

        $role->permissions()->sync($permissions);
    }

    public function vendedor()
    {
        $permissions = [];
        $role = Role::where('name', 'Vendedor')->first();

        foreach (PermissionsSeeder::PERMISSIONS_VENDEDOR as $permission) {
            $per = Permission::where('name', $permission)->first();
            $permissions[] = $per->id;
        }

        $role->permissions()->sync($permissions);
    }
}
