<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Division;
use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $branch = Branch::where('name', 'El Tigre')->first();
        $projectIds = Project::pluck('id')->toArray();

        $equipment = [
            [
                // Identificadores y seriales técnicos
                'branch_id'               => $branch->id,
                'internal_code'           => '00-AJHG', // Placa de Venezuela
                'owner'                   => 'Dueño 1', // Placa de Venezuela
                'placa'                   => 'ABC123V', // Placa de Venezuela
                'serial_niv'              => 'WVWZZZ3CZK1234567', // VIN (NIV)
                'body_serial_number'      => 'BOD9876543210',    // Serial de Carrocería
                'chassis_serial_number'   => 'CHA0123456789',    // Serial de Chasis
                'engine_serial_number'    => 'ENG555444333222',  // Serial del Motor

                // Atributos del vehículo
                'vehicle_model'           => 'Fortuner',
                'brand_name'              => 'Toyota',
                'model_year'              => 2022,
                'color'                   => 'Blanco Perla',
                'origin'                  => 'Japón',

                // Identificador legal/administrativo
                'racda'                   => 'No',
            ],
            [
                // Identificadores y seriales técnicos
                'branch_id'               => $branch->id,
                'internal_code'           => '01-AJHG',
                'owner'                   => 'Dueño 2',
                'placa'                   => 'DEF456G',
                'serial_niv'              => '1GCRF4B0P85790432',
                'body_serial_number'      => 'BOD222111000999',
                'chassis_serial_number'   => 'CHA998877665544',
                'engine_serial_number'    => 'ENG123456789012',

                // Atributos del vehículo
                'vehicle_model'           => 'Explorer',
                'brand_name'              => 'Ford',
                'model_year'              => 2018,
                'color'                   => 'Gris Oscuro',
                'origin'                  => 'Estados Unidos',

                // Identificador legal/administrativo
                'racda'                   => 'Si',
            ],
        ];

        foreach ($equipment as $data) {

            $equipment = $branch->equipment()->firstOrCreate(
                ['internal_code' => $data['internal_code']],
                $data
            );

            if (!empty($projectIds)) {
                $equipment->projects()->syncWithoutDetaching([$projectIds[0]]);
            }
        }
    }
}
