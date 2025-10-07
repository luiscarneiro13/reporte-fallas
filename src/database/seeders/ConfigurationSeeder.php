<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuration = Configuration::first();

        if ($configuration === null) {
            $configuration = new Configuration();
            $configuration->tax = 16;
            $configuration->discount = 0;
            $configuration->save();
        }
    }
}
