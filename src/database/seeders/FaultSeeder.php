<?php

namespace Database\Seeders;

use App\Models\Fault;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faults = [
            [
                'name' => 'Engine Failure',
                'description' => 'The engine is not starting or has stopped working.',
            ],
            [
                'name' => 'Brake Issues',
                'description' => 'Problems with the braking system, including reduced effectiveness or failure.',
            ],
            [
                'name' => 'Electrical Problems',
                'description' => 'Issues with the vehicle\'s electrical system, such as battery or wiring faults.',
            ],
            [
                'name' => 'Transmission Trouble',
                'description' => 'Difficulties in shifting gears or transmission slipping.',
            ],
            [
                'name' => 'Suspension Problems',
                'description' => 'Issues with the vehicle\'s suspension system, leading to poor ride quality or handling.',
            ],
        ];

        foreach ($faults as $fault) {
            Fault::create($fault);
        }
    }
}
