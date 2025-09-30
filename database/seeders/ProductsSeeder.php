<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\TypeArticle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cantProducts = 20;

        $branch = Branch::where('name', 'El Tigre')->first();
        $brand = Brand::where('name', 'PDV')->first();
        $typeArticle = TypeArticle::where('name', 'LUBRICANTE')->first();

        for ($i = 1; $i <= $cantProducts; $i++) {
            $prod =  "Aceite de prueba " . $i;
            $product = Product::where('name', $prod)->first();

            if (!$product) {
                $product = Product::create([
                    'branch_id' => $branch->id,
                    'name' => $prod,
                    'description' => '',
                    'available_qty' => 10,
                    'price' => 99.99,
                    'brand_id' => $brand->id,
                    'type_article_id' => $typeArticle->id,
                ]);
            }
        }
    }
}
