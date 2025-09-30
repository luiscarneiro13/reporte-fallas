<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use App\Models\UserBranch;
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
        $user = User::where('name', 'Super Admin')->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
            ]);
            $rol = Role::where('name', "Super Admin")->first();
            $user->roles()->sync([$rol->id]);
        }
        $rol = Role::where('name', "Super Admin")->first();
        $user->roles()->sync([$rol->id]);



        $user2 = User::where('name', 'Admin')->first();
        if (!$user2) {
            $user2 = User::create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
            ]);
        }
        $rol = Role::where('name', "Admin")->first();
        $user2->roles()->sync([$rol->id]);



        $user3 = User::where('name', 'Admin Sucursal')->first();
        if (!$user3) {
            $user3 = User::create([
                'name' => 'Admin Sucursal',
                'email' => 'adminsucursal@gmail.com',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
            ]);
        }
        $rol = Role::where('name', "Admin Sucursal")->first();
        $user3->roles()->sync([$rol->id]);



        $user4 = User::where('name', 'Cajero')->first();
        if (!$user4) {
            $user4 = User::create([
                'name' => 'Cajero',
                'email' => 'cajero@gmail.com',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'phone' => '123456789',
            ]);
        }
        $rol = Role::where('name', "Cajero")->first();
        $user4->roles()->sync([$rol->id]);
        $branch = Branch::where('name', 'Cabimas')->first();
        $userBranch = new UserBranch();
        $userBranch->branch_id = $branch->id;
        $user4->userBranches()->save($userBranch);



        $user5 = User::where('name', 'Vendedor')->first();
        if (!$user5) {
            $user5 = User::create([
                'name' => 'Vendedor',
                'email' => 'vendedor@gmail.com',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'phone' => '123456789',
            ]);
        }
        $rol = Role::where('name', "Vendedor")->first();
        $user5->roles()->sync([$rol->id]);
        $branch = Branch::where('name', 'Cabimas')->first();
        $userBranch = new UserBranch();
        $userBranch->branch_id = $branch->id;
        $user5->userBranches()->save($userBranch);
    }
}
