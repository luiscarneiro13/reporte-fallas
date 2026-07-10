<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\ContractType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $contractTypes = [
            [
                'name' => 'Personal fijo',
            ],
            [
                'name' => 'Personal eventual',
            ],
            [
                'name' => 'Personal en pruebas',
            ]
        ];

        $branch = Branch::where('name', 'El Tigre')->first();

        foreach ($contractTypes as $item) {
            $branch->contractTypes()->firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
