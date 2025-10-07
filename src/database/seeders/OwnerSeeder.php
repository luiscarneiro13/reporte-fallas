<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $owners = [
            ['first_name' => 'Propietario 1', 'last_name' => 'Propietario 1 Apellido'],
            ['first_name' => 'Propietario 2', 'last_name' => 'Propietario 2 Apellido']
        ];

        foreach ($owners as $owner) {
            \App\Models\Owner::create($owner);
        }
    }
}
