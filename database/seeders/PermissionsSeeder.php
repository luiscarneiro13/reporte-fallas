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
        'Proyectos Ver',
        'Proyectos Crear',
        'Proyectos Editar',
        'Proyectos Eliminar',
        'Propietarios Ver',
        'Propietarios Crear',
        'Propietarios Editar',
        'Propietarios Eliminar',
        'Equipos Ver',
        'Equipos Crear',
        'Equipos Editar',
        'Equipos Eliminar',
        'Fallas Ver',
        'Fallas Crear',
        'Fallas Editar',
        'Fallas Eliminar',
        'Areas de Servicio Ver',
        'Areas de Servicio Crear',
        'Areas de Servicio Editar',
        'Areas de Servicio Eliminar',
    ];
    public const PERMISSIONS_SUPER_ADMIN = [
        'Menu Conf Super Admin',
        'Menu Conf Admin',
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
        'Proyectos Ver',
        'Proyectos Crear',
        'Proyectos Editar',
        'Proyectos Eliminar',
        'Propietarios Ver',
        'Propietarios Crear',
        'Propietarios Editar',
        'Propietarios Eliminar',
        'Equipos Ver',
        'Equipos Crear',
        'Equipos Editar',
        'Equipos Eliminar',
        'Fallas Ver',
        'Fallas Crear',
        'Fallas Editar',
        'Fallas Eliminar',
        'Areas de Servicio Ver',
        'Areas de Servicio Crear',
        'Areas de Servicio Editar',
        'Areas de Servicio Eliminar',
    ];

    public const PERMISSIONS_ADMIN = [
        'Menu Conf Admin',
        'Usuarios Ver',
        'Usuarios Crear',
        'Usuarios Editar',
        'Usuarios Eliminar',
        'Proyectos Ver',
        'Proyectos Crear',
        'Proyectos Editar',
        'Proyectos Eliminar',
        'Propietarios Ver',
        'Propietarios Crear',
        'Propietarios Editar',
        'Propietarios Eliminar',
        'Equipos Ver',
        'Equipos Crear',
        'Equipos Editar',
        'Equipos Eliminar',
        'Fallas Ver',
        'Fallas Crear',
        'Fallas Editar',
        'Fallas Eliminar',
        'Areas de Servicio Ver',
        'Areas de Servicio Crear',
        'Areas de Servicio Editar',
        'Areas de Servicio Eliminar',
    ];

    public const PERMISSIONS_ADMIN_SUCURSAL = [
        'Menu Conf Admin',
        'Usuarios Ver',
        'Usuarios Crear',
        'Usuarios Editar',
        'Usuarios Eliminar',
        'Proyectos Ver',
        'Proyectos Crear',
        'Proyectos Editar',
        'Proyectos Eliminar',
        'Propietarios Ver',
        'Propietarios Crear',
        'Propietarios Editar',
        'Propietarios Eliminar',
        'Equipos Ver',
        'Equipos Crear',
        'Equipos Editar',
        'Equipos Eliminar',
        'Fallas Ver',
        'Fallas Crear',
        'Fallas Editar',
        'Fallas Eliminar',
        'Areas de Servicio Ver',
        'Areas de Servicio Crear',
        'Areas de Servicio Editar',
        'Areas de Servicio Eliminar',
    ];

    public const PERMISSIONS_SUPERVISOR = [
        'Proyectos Ver',
        'Proyectos Crear',
        'Proyectos Editar',
        'Proyectos Eliminar',
        'Equipos Ver',
        'Equipos Crear',
        'Equipos Editar',
        'Equipos Eliminar',
        'Fallas Ver',
        'Fallas Crear',
        'Fallas Editar',
        'Fallas Eliminar',
        'Areas de Servicio Ver',
        'Areas de Servicio Crear',
        'Areas de Servicio Editar',
        'Areas de Servicio Eliminar',
    ];

    public const PERMISSIONS_OPERADOR = [
        'Proyectos Ver',
        'Fallas Ver',
        'Fallas Crear',
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
