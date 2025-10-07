<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::where('name', 'El Tigre')->first();

        $projects = [
            [
                'name' => 'Project Alpha',
                'description' => 'Description for Project Alpha',
            ],
            [
                'name' => 'Project Beta',
                'description' => 'Description for Project Beta',
            ],
        ];

        foreach ($projects as $item) {
            $branch->projects()->create($item);
        }
    }
}
