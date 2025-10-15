<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\FaultStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaultStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faultStatus = [
            [
                'name' => 'En espera de repuesto',
            ],
            [
                'name' => 'En ejecución',
            ],
            [
                'name' => ' En espera por coordinación con el cliente',
            ]
        ];

        $branch = Branch::where('name', 'El Tigre')->first();

        foreach ($faultStatus as $item) {
            $branch->faultStatus()->firstOrCreate(
                ['name' => trim($item['name'])],
                $item
            );
        }
    }
}
