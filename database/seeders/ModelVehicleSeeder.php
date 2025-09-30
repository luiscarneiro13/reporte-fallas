<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\ModelVehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModelVehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marcas = ['Ford Explorer 2002', 'Ford Explorer 2003', 'Ford Explorer 2004', 'Ford Explorer 2005'];
        $branch = Branch::where('name', 'El Tigre')->first();

        foreach ($marcas as $item) {
            $modelVehicle = ModelVehicle::where('name', $item)->where('branch_id', $branch->id)->first();
            if (!$modelVehicle) {
                $modelVehicle = ModelVehicle::create([
                    'name' => $item,
                    'branch_id' => $branch->id,
                ]);
            }
        }
    }
}
