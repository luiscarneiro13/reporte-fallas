<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\TypeArticle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = ['LUBRICANTE', 'REFRIGERANTE', 'GRASA', 'FILTRO DE ACEITE', 'FILTRO DE AIRE', 'FILTRO DE GASOLINA'];
        $branch = Branch::where('name', 'Cabimas')->first();

        foreach ($tipos as $item) {
            $typeArticle = TypeArticle::where('name', $item)->where('branch_id', $branch->id)->first();
            if (!$typeArticle) {
                $typeArticle = TypeArticle::create([
                    'name' => $item,
                    'branch_id' => $branch->id,
                ]);
            }
        }
    }
}
