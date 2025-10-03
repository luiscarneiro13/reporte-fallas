<?php

namespace App\Helpers;

class Permisos
{

    public const ROLES_MAP = ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3];

    public const PERMISSIONS_MAP = [

        'Menu Conf Super Admin' => [0],

        // Roles ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Roles Ver' => [0],
        'Roles Crear' => [0],
        'Roles Editar' => [0],
        'Roles Eliminar' => [0],

        // Permisos ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Permisos Ver' => [0],
        'Permisos Crear' => [0],
        'Permisos Editar' => [0],
        'Permisos Eliminar' => [0],

        'Menu Conf Admin' => [0, 1],

        // Usuarios ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Usuarios Ver' => [0, 1],
        'Usuarios Crear' => [0, 1],
        'Usuarios Editar' => [0, 1],
        'Usuarios Eliminar' => [0, 1],

        //Proyectos ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Proyectos Ver' => [0, 1, 2, 3],
        'Proyectos Crear' => [0, 1, 2],
        'Proyectos Editar' => [0, 1, 2],
        'Proyectos Eliminar' => [0, 1, 2],

        //Propietarios ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Propietarios Ver' => [0, 1],
        'Propietarios Crear' => [0, 1],
        'Propietarios Editar' => [0, 1],
        'Propietarios Eliminar' => [0, 1],

        //Equipos ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Equipos Ver' => [0, 1, 2],
        'Equipos Crear' => [0, 1, 2],
        'Equipos Editar' => [0, 1, 2],
        'Equipos Eliminar' => [0, 1, 2],

        //Fallas ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Fallas Ver' => [0, 1, 2, 3],
        'Fallas Crear' => [0, 1, 2, 3],
        'Fallas Editar' => [0, 1, 2],
        'Fallas Eliminar' => [0, 1, 2],

        //Areas de servicio ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Areas de Servicio Ver' => [0, 1, 2],
        'Areas de Servicio Crear' => [0, 1, 2],
        'Areas de Servicio Editar' => [0, 1, 2],
        'Areas de Servicio Eliminar' => [0, 1, 2],
    ];
}
