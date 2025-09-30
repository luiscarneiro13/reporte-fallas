<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public const PERMISSIONS_ALL = [
        'Menu Conf Super Admin',
        'Menu Conf Admin',
        'Menu Conf Admin Sucursal',
        'Usuarios Ver',
        'Usuarios Crear',
        'Usuarios Editar',
        'Usuarios Eliminar',
        'Roles Ver',
        'Roles Crear',
        'Roles Editar',
        'Roles Eliminar',
        'Permisos Ver',
        'Permisos Crear',
        'Permisos Editar',
        'Permisos Eliminar',
        'Clientes Ver',
        'Clientes Crear',
        'Clientes Editar',
        'Clientes Eliminar',
        'Ventas dia Ver',
        'Ventas dia Editar',
        'Historial ventas Ver',
        'Historial ventas Editar',
        'Inventario Ver',
        'Inventario Editar',
        'Vender',
    ];
    public const PERMISSIONS_SUPER_ADMIN = [
        'Menu Conf Super Admin',
        'Menu Conf Admin',
        'Menu Conf Admin Sucursal',
        'Usuarios Ver',
        'Usuarios Crear',
        'Usuarios Editar',
        'Usuarios Eliminar',
        'Roles Ver',
        'Roles Crear',
        'Roles Editar',
        'Roles Eliminar',
        'Permisos Ver',
        'Permisos Crear',
        'Permisos Editar',
        'Permisos Eliminar',
        'Clientes Ver',
        'Clientes Crear',
        'Clientes Editar',
        'Clientes Eliminar',
        'Ventas dia Ver',
        'Ventas dia Editar',
        'Historial ventas Ver',
        'Historial ventas Editar',
        'Inventario Ver',
        'Inventario Editar',
        'Vender',
    ];

    public const PERMISSIONS_ADMIN = [
        'Menu Conf Admin',
        'Vender',
    ];

    public const PERMISSIONS_ADMIN_SUCURSAL = [
        'Menu Conf Admin Sucursal',
        'Ventas dia Ver',
        'Ventas dia Editar',
        'Historial ventas Ver',
        'Historial ventas Editar',
        'Vender',
        'Inventario Ver',
        'Inventario Editar',
    ];

    public const PERMISSIONS_CAJERO = [
        'Ventas dia Ver',
        'Ventas dia Editar',
        'Historial ventas Ver',
        'Historial ventas Editar',
        'Vender',
    ];

    public const PERMISSIONS_VENDEDOR = [
        'Vender',
    ];

    public function run(): void
    {
        $this->permissionsAll();
    }

    // Se crean todos los permisos en la base de datos
    public function permissionsAll()
    {
        foreach (self::PERMISSIONS_ALL as $value) {
            $permission = Permission::where('name', $value)->first();
            if (is_null($permission)) {
                Permission::create(['name' => $value, "guard_name" => "sanctum"]);
            }
        }
    }
}
