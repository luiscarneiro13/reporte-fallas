<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Cargo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $cargos = [
            ['name' => 'Coor. sihoa'],
            ['name' => 'Supervisor de campo'],
            ['name' => 'Coor. operaciones'],
            ['name' => 'Coor. gestión de calidad'],
            ['name' => 'Administrador de contratos'],
            ['name' => 'Asistente administrativo'],
            ['name' => 'Gerente'],
            ['name' => 'Operador de Maquinaria'],
        ];

        $branch = Branch::where('name', 'El Tigre')->first();

        foreach ($cargos as $item) {
            $branch->cargos()->firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
