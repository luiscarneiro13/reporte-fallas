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
        'Administradores Ver' => [0, 1],
        'Administradores Crear' => [0, 1],
        'Administradores Editar' => [0, 1],
        'Administradores Eliminar' => [0, 1],

        // Usuarios ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Supervisores Ver' => [0, 1],
        'Supervisores Crear' => [0, 1],
        'Supervisores Editar' => [0, 1],
        'Supervisores Eliminar' => [0, 1],

        // Usuarios ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Operadores Ver' => [0, 1],
        'Operadores Crear' => [0, 1],
        'Operadores Editar' => [0, 1],
        'Operadores Eliminar' => [0, 1],

        // Editar mi empresa ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Empresa Editar' => [1],

        //Divisiones ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Divisiones Ver' => [0, 1, 2],
        'Divisiones Crear' => [0, 1, 2],
        'Divisiones Editar' => [0, 1, 2],
        'Divisiones Eliminar' => [0, 1, 2],

        //Estatus de fallas ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Estatus de fallas Ver' => [0, 1, 2],
        'Estatus de fallas Crear' => [0, 1, 2],
        'Estatus de fallas Editar' => [0, 1, 2],
        'Estatus de fallas Eliminar' => [0, 1, 2],

        //Estatus de repuestos ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Estatus de repuestos Ver' => [0, 1, 2],
        'Estatus de repuestos Crear' => [0, 1, 2],
        'Estatus de repuestos Editar' => [0, 1, 2],
        'Estatus de repuestos Eliminar' => [0, 1, 2],

        //Areas de servicio ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Areas de Servicio Ver' => [0, 1, 2],
        'Areas de Servicio Crear' => [0, 1, 2],
        'Areas de Servicio Editar' => [0, 1, 2],
        'Areas de Servicio Eliminar' => [0, 1, 2],

        //Clientes ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Clientes Ver' => [0, 1],
        'Clientes Crear' => [0, 1],
        'Clientes Editar' => [0, 1],
        'Clientes Eliminar' => [0, 1],

        //Proyectos ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Proyectos Ver' => [0, 1, 2, 3],
        'Proyectos Crear' => [0, 1, 2],
        'Proyectos Editar' => [0, 1, 2],
        'Proyectos Eliminar' => [0, 1, 2],

        //Empleados ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Empleados Ver' => [0, 1, 2],
        'Empleados Crear' => [0, 1, 2],
        'Empleados Editar' => [0, 1, 2],
        'Empleados Eliminar' => [0, 1, 2],

        //Ejecutores ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Ejecutores Ver' => [0, 1, 2],
        'Ejecutores Crear' => [0, 1, 2],
        'Ejecutores Editar' => [0, 1, 2],
        'Ejecutores Eliminar' => [0, 1, 2],

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
    ];
}
