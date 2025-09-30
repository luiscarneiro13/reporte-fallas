<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::where('name', 'Cabimas')->first();
        $services = Service::where('branch_id', $branch->id)->get();

        if (count($services) == 0) {
            $serv = Service::create([
                'name' => 'Lavado y aspirado',
                'branch_id' => $branch->id,
                'price' => 30,
            ]);
        }
    }
}
