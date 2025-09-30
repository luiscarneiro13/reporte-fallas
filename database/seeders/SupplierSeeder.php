<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::where('name', 'El Tigre')->first();
        $supplier = Supplier::where('name', 'Sin Proveedor')->first();

        if (!$supplier) {
            $supplier = Supplier::create([
                'branch_id' => $branch->id,
                'name' => 'Sin Proveedor',
                'address' => 'Dirección de prueba',
                'phone' => '0424-5555555',
                'email' => 'email@ejemplo.com',
            ]);
        }
    }
}
