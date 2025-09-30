<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\DeliveryNote;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10000; $i++) {

            $sale = Sale::create([
                "customer_id" => 8,
                "method_payment_id" => 5,
                "branch_id" => 1,
                "rate" => 36.08,
                "average_rate" => 37,
                "total_items" => 1,
                "total" => 5.13,
                "service" => 0,
                "paid" => 1,
                "total_bs" => 1185,
            ]);

            SaleDetail::create([
                "sale_id" => $sale->id,
                "product" => "RADIADOR 2 - RADIADOR - CABIMAS - RADIADOR",
                "qty" => 1,
                "rate" => 36.08,
                "price" => 5,
                "price_bs" => 185,
                "rate" => 36.08,
                "average_rate" => 37,
                "sub_total" => 5,
                "sub_total_bs" => 185,
                "product_service_id" => 3,
            ]);

            DeliveryNote::create([
                "branch_id" => 1,
                "sale_id" => $sale->id,
                "name" => "LUIS CARNEIRO",
                "rif" => "16572916",
                "address" => "EL TIGRE",
                "rate" => 36.08,
                "average_rate" => 37,
                "sub_total" => 5.13,
                "tax" => 16,
                "total" => 5.96,
            ]);
        }
    }
}
