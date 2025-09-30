<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marcas = ['PDV', 'SHELL', 'MAHLE', 'PUROLATOR', 'FRAM', 'WIX', 'SAKURA', 'MANN FILTER', 'BALDWIN'];
        $branch = Branch::where('name', 'Cabimas')->first();

        foreach ($marcas as $item) {
            $brand = Brand::where('name', $item)->where('branch_id', $branch->id)->first();
            if (!$brand) {
                $brand = Brand::create([
                    'name' => $item,
                    'branch_id' => $branch->id,
                ]);
            }
        }
    }
}
