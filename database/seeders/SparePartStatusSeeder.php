<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\SparePartStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SparePartStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $sparePartStatus = [
            [
                'name' => 'Por solicitar',
            ],
            [
                'name' => 'Solicitado',
            ],
            [
                'name' => 'En almacén',
            ]
        ];

        $branch = Branch::where('name', 'El Tigre')->first();

        foreach ($sparePartStatus as $item) {
            $branch->sparePartStatus()->firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
