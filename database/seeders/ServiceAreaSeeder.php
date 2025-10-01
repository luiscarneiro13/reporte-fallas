<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceAreas = [
            [
                'name' => 'Engine Repair',
                'description' => 'Services related to engine diagnostics, repairs, and maintenance.',
            ],
            [
                'name' => 'Brake Service',
                'description' => 'Comprehensive brake system inspections, repairs, and replacements.',
            ],
            [
                'name' => 'Electrical Systems',
                'description' => 'Diagnostics and repairs for vehicle electrical systems, including batteries and wiring.',
            ],
            [
                'name' => 'Transmission Service',
                'description' => 'Maintenance and repair services for vehicle transmissions.',
            ],
            [
                'name' => 'Suspension and Steering',
                'description' => 'Services focused on suspension and steering system repairs and alignments.',
            ],
        ];

        foreach ($serviceAreas as $area) {
            \App\Models\ServiceArea::create($area);
        }
    }
}
