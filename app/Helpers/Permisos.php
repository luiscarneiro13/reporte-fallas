<?php

namespace App\Helpers;

class Permisos
{

    public const ROLES_MAP = ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4];

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

        //Fallas ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4]
        'Dashboard menu Ver' => [0, 1, 2, 4],

        // Usuarios ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Administradores Ver' => [0],
        'Administradores Crear' => [0],
        'Administradores Editar' => [0],
        'Administradores Eliminar' => [0],

        // Usuarios ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Supervisores Ver' => [0],
        'Supervisores Crear' => [0],
        'Supervisores Editar' => [0],
        'Supervisores Eliminar' => [0],

        // Usuarios ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Operadores Ver' => [0],
        'Operadores Crear' => [0],
        'Operadores Editar' => [0],
        'Operadores Eliminar' => [0],

        // Editar mi empresa ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Empresa Editar' => [1],

        //Divisiones ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4]
        'Divisiones Ver' => [0, 1, 2, 4],
        'Divisiones Crear' => [0, 1, 2, 4],
        'Divisiones Editar' => [0, 1, 2, 4],
        'Divisiones Eliminar' => [0, 1, 2, 4],

        //Estatus de fallas ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4]
        'Estatus de fallas Ver' => [0, 1, 2, 4],
        'Estatus de fallas Crear' => [0, 1, 2, 4],
        'Estatus de fallas Editar' => [0, 1, 2, 4],
        'Estatus de fallas Eliminar' => [0, 1, 2, 4],

        //Estatus de repuestos ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4]
        'Estatus de repuestos Ver' => [0, 1, 2, 4],
        'Estatus de repuestos Crear' => [0, 1, 2, 4],
        'Estatus de repuestos Editar' => [0, 1, 2, 4],
        'Estatus de repuestos Eliminar' => [0, 1, 2, 4],

        //Areas de servicio ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4]
        'Areas de Servicio Ver' => [0, 1, 2, 4],
        'Areas de Servicio Crear' => [0, 1, 2, 4],
        'Areas de Servicio Editar' => [0, 1, 2, 4],
        'Areas de Servicio Eliminar' => [0, 1, 2, 4],

        //Tipos de contrato ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4]
        'Tipos de Contrato Ver' => [0, 1, 2, 4],
        'Tipos de Contrato Crear' => [0, 1],
        'Tipos de Contrato Editar' => [0, 1],
        'Tipos de Contrato Eliminar' => [0, 1],

        //Cargos ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4]
        'Cargos Ver' => [0, 1, 2, 4],
        'Cargos Crear' => [0, 1, 2, 4],
        'Cargos Editar' => [0, 1, 2, 4],
        'Cargos Eliminar' => [0, 1, 2, 4],

        //Clientes ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3]
        'Clientes Ver' => [0, 1],
        'Clientes Crear' => [0, 1],
        'Clientes Editar' => [0, 1],
        'Clientes Eliminar' => [0, 1],

        //Proyectos ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4]
        'Proyectos Ver' => [0, 1, 2, 3, 4],
        'Proyectos Crear' => [0, 1, 2, 4],
        'Proyectos Editar' => [0, 1, 2, 4],
        'Proyectos Eliminar' => [0, 1, 2, 4],

        //Empleados ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4]
        'Empleados Ver' => [0, 1, 2, 4],
        'Empleados Crear' => [0, 1, 2, 4],
        'Empleados Editar' => [0, 1, 2, 4],
        'Empleados Eliminar' => [0, 1, 2, 4],

        //Incidencias de Empleados ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4]
        'Incidencias de Empleados Ver' => [0, 1, 2, 4],
        'Incidencias de Empleados Crear' => [0, 1, 2, 4],
        'Incidencias de Empleados Editar' => [0, 1, 2, 4],
        'Incidencias de Empleados Eliminar' => [0, 1, 2, 4],

        //Ejecutores ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4]
        'Ejecutores Ver' => [0, 1, 2, 4],
        'Ejecutores Crear' => [0, 1, 2, 4],
        'Ejecutores Editar' => [0, 1, 2, 4],
        'Ejecutores Eliminar' => [0, 1, 2, 4],

        //Equipos ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4]
        'Equipos Ver' => [0, 1, 2, 4],
        'Equipos Crear' => [0, 1, 2, 4],
        'Equipos Editar' => [0, 1, 2, 4],
        'Equipos Eliminar' => [0, 1, 2, 4],

        //Tipos de equipo ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4]
        'Tipos de equipo Ver' => [0, 1, 2, 4],
        'Tipos de equipo Crear' => [0, 1, 2, 4],
        'Tipos de equipo Editar' => [0, 1, 2, 4],
        'Tipos de equipo Eliminar' => [0, 1, 2, 4],

        //Fallas ['Super Admin' => 0, 'Admin' => 1, 'Supervisor' => 2, 'Operador' => 3, 'Coordinador' => 4]
        'Fallas Ver' => [0, 1, 2, 3, 4],
        'Fallas Crear' => [0, 1, 2, 3, 4],
        'Fallas Editar' => [0, 1, 2, 4],
        'Fallas Eliminar' => [0, 1, 2, 4],
    ];
}
