<?php

namespace Database\Seeders;

use App\Models\DailyRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DailyRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dailyRate = DailyRate::first();

        if ($dailyRate === null) {
            $dailyRate = new DailyRate();
            $dailyRate->rate = 36.08;
            $dailyRate->average_rate = 37.00;
            $dailyRate->save();
        }
    }
}
