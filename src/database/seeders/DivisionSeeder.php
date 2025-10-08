<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::where('name', 'El Tigre')->first();

        $divisions = [
            [
                'name' => 'P.Pozos de Producción a Pozos',
                'description' => 'P.Pozos de Producción a Pozos.',
            ],
            [
                'name' => 'Equipos Transversales',
                'description' => 'Equipos Transversales.',
            ],
            [
                'name' => 'Mantenimiento y Logística',
                'description' => 'Mantenimiento y Logística.',
            ],
        ];

        foreach ($divisions as $item) {
            $branch->divisions()->create($item);
        }
    }
}
