<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::where('name', 'El Tigre')->first();

        if (!$branch) {
            $branch = Branch::create([
                'name' => 'El Tigre',
                'description' => 'Sucursal El Tigre',
                'email' => 'carneiroluis2@gmail.com',
                'phone' => '04248807465',
                'rif' => '04248807465',
                'address' => 'El Tigre',
                // 'logo' => asset('logo.webp') Sin logo para que se muestre pro defecto el predefinido
            ]);
        }
    }
}
