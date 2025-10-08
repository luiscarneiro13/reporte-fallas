<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::where('name', 'El Tigre')->first();

        $serviceAreas = [
            [
                'name' => 'Mecánica liviana',
                'description' => 'Mecánica liviana.',
            ],
            [
                'name' => 'Mecánica pesada',
                'description' => 'Mecánica pesada.',
            ],
            [
                'name' => 'Electricidad automotriz',
                'description' => 'Electricidad automotriz.',
            ],
            [
                'name' => 'Instalaciones eléctricas',
                'description' => 'Instalaciones eléctricas.',
            ],
            [
                'name' => 'Soldadura',
                'description' => 'Soldadura.',
            ],
            [
                'name' => 'Latonería',
                'description' => 'Latonería.',
            ],
            [
                'name' => 'Refrigeración',
                'description' => 'Refrigeración.',
            ],
            [
                'name' => 'Instrumentación',
                'description' => 'Instrumentación.',
            ],
        ];

        foreach ($serviceAreas as $item) {
            $branch->serviceAreas()->create($item);
        }
    }
}
