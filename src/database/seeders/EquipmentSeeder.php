<?php

namespace Database\Seeders;

use App\Models\Branch;
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

        $equipment = [
            [
                'placa' => 'ABC123',
                'serial' => 'SN123456789',
                'year' => '2020',
                'color' => 'Red',
                'origin' => 'USA',
                'racda' => 'RACDA001',
            ],
            [
                'placa' => 'DEF456',
                'serial' => 'SN987654321',
                'year' => '2021',
                'color' => 'Blue',
                'origin' => 'Canada',
                'racda' => 'RACDA002',
            ],
        ];

        foreach ($equipment as $item) {
            $branch->equipment()->create($item);
        }
    }
}
