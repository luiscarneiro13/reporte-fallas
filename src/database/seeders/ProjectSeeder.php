<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Division;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::where('name', 'El Tigre')->first();
        $customerIds = Customer::pluck('id')->toArray();
        $divisionIds = Division::pluck('id')->toArray();

        if (empty($customerIds) || empty($divisionIds)) {
            return; // Detener la ejecución si no hay clientes
        }

        $projects = [
            [
                'customer_id' => $customerIds[array_rand($customerIds)],
                'division_id' => $divisionIds[array_rand($divisionIds)],
                'name' => 'Project Alpha',
                'description' => 'Description for Project Alpha',
                'geographic_area' => 'Area geográfica 1',
                'contract_number' => 'CN-001'
            ],
            [
                'customer_id' => $customerIds[array_rand($customerIds)],
                'division_id' => $divisionIds[array_rand($divisionIds)],
                'name' => 'Project Beta',
                'description' => 'Description for Project Beta',
                'geographic_area' => 'Area geográfica 2',
                'contract_number' => 'CN-002'
            ],
        ];

        foreach ($projects as $item) {
            $branch->projects()->create($item);
        }
    }
}
