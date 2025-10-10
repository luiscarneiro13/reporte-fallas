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
                'name' => 'Fallo de Motor',
            ],
            [
                'name' => 'Problemas de Frenos',
            ],
            [
                'name' => 'Problemas Eléctricos',
            ],
            [
                'name' => 'Fallo de Transmisión',
            ],
            [
                'name' => 'Problemas de Suspensión',
            ],
        ];

        $branch = Branch::where('name', 'El Tigre')->first();

        foreach ($faultStatus as $item) {
            $branch->faultStatus()->create($item);
        }
    }
}
