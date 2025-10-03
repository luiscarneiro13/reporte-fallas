<?php

namespace Database\Seeders;

use App\Helpers\Permisos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear o actualizar los roles dinámicamente usando ROLES_MAP
        foreach (Permisos::ROLES_MAP as $roleName => $index) {

            // firstOrCreate busca el rol por 'name'.
            // Si lo encuentra, lo devuelve. Si no, lo crea con los atributos dados.
            Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'sanctum'] // Atributos para la creación
            );
        }
    }
}
