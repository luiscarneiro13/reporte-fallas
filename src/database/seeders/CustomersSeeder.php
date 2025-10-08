<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use App\Models\UserBranch;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CustomersSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('name', 'El Tigre')->first();

        $customers = [
            [
                'name' => 'Empresas polar',
                'address' => 'Av. Jesús Subero',
                'phone' => '0283-1234567',
                'email' => 'cualquieremail1@gmail.com'
            ],
            [
                'name' => 'Pepsi',
                'address' => 'Av. Jesús Subero',
                'phone' => '0283-1234567',
                'email' => 'cualquieremail2@gmail.com'
            ],
            [
                'name' => 'Coca Cola',
                'address' => 'Av. Jesús Subero',
                'phone' => '0283-1234567',
                'email' => 'cualquieremail3@gmail.com'
            ],
            [
                'name' => 'Banesco',
                'address' => 'Av. Jesús Subero',
                'phone' => '0283-1234567',
                'email' => 'cualquieremail4@gmail.com'
            ],
        ];

        foreach ($customers as $item) {
            $branch->customers()->create($item);
        }
    }
}
