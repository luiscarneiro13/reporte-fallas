<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\MethodPayment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MethodPaymentSeeder extends Seeder
{

    public function run(): void
    {
        $methods = [
            'bolivares_efectivo' => 'BOLIVARES EN EFECTIVO',
            'dolares_efectivo' => 'DOLARES EN EFECTIVO',
            'pago_movil' => 'PAGO MÓVIL',
            'biopago' => 'BIOPAGO',
            'punto_venta_venezuela' => 'PUNTO DE VENTA VENEZUELA',
            'punto_venta_banesco' => 'PUNTO DE VENTA BANESCO',
            'zelle' => 'ZELLE',
            'mixto' => 'MIXTO'
        ];

        $branch = Branch::where('name', 'El Tigre')->first();

        foreach ($methods as $key => $item) {
            $methodPayment = MethodPayment::where('name', $item)->where('branch_id', $branch->id)->first();
            if (!$methodPayment) {
                $methodPayment = MethodPayment::create([
                    'slug' => $key,
                    'name' => $item,
                    'branch_id' => $branch->id,
                ]);
            } else {
                $methodPayment->slug = $key;
                $methodPayment->name = $item;
                $methodPayment->branch_id = $branch->id;
                $methodPayment->save();
            }
        }
    }
}
