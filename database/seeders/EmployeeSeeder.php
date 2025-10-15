<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::where('name', 'El Tigre')->first();

        $employees = [
            [
                'branch_id'             => $branch->id,
                'identification_number' => '12345678',
                'first_name'            => 'Pedro',
                'last_name'             => 'Pozos',
                'email'                 => 'pedro.pozos@example.com',
                'phone_number'          => '0414-1234567',
                'address'               => 'Sector Producción, El Tigre',
                'executor'              => true,
                'external'              => true,
            ],
            [
                'branch_id'             => $branch->id,
                'identification_number' => '87654321',
                'first_name'            => 'Carla',
                'last_name'             => 'Transversal',
                'email'                 => 'carla.transversal@example.com',
                'phone_number'          => '0412-7654321',
                'address'               => 'Zona Transversal, El Tigre',
                'executor'              => true,
                'external'              => false,
            ],
            [
                'branch_id'             => $branch->id,
                'identification_number' => '11223344',
                'first_name'            => 'Luis',
                'last_name'             => 'Logística',
                'email'                 => 'luis.logistica@example.com',
                'phone_number'          => '0424-1122334',
                'address'               => 'Base de Mantenimiento, El Tigre',
                'executor'              => false,
                'external'              => false,
            ],
        ];

        foreach ($employees as $data) {
            $branch->employees()->create($data);
        }
    }
}
