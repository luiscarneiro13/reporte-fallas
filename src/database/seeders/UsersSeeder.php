<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use App\Models\UserBranch;
use App\Services\UserService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $branch = Branch::where('name', 'El Tigre')->first();

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => '123456',
                'phone' => '123456789',
                'role' => 'Super Admin'
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => '123456',
                'phone' => '123456789',
                'role' => 'Admin',
                'branch_id' => $branch->id
            ],
            [
                'name' => 'Supervisor',
                'email' => 'supervisor@gmail.com',
                'password' => '123456',
                'phone' => '123456789',
                'role' => 'Supervisor',
                'branch_id' => $branch->id
            ],
            [
                'name' => 'Operador',
                'email' => 'operador@gmail.com',
                'password' => '123456',
                'phone' => '123456789',
                'role' => 'Operador',
                'branch_id' => $branch->id
            ]
        ];

        foreach ($users as $item) {
            UserService::insertUserRole($item);
        }
    }
}
