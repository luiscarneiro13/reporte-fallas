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
                'name' => 'Customer Service',
                'description' => 'Handles customer inquiries, complaints, and service scheduling.',
            ],
            [
                'name' => 'Technical Support',
                'description' => 'Provides technical assistance and support for vehicle issues.',
            ],
            [
                'name' => 'Sales and Marketing',
                'description' => 'Responsible for promoting services and managing sales operations.',
            ],
            [
                'name' => 'Parts and Inventory',
                'description' => 'Manages parts inventory and ensures availability for repairs.',
            ]
        ];

        foreach ($divisions as $item) {
            $branch->divisions()->create($item);
        }
    }
}
