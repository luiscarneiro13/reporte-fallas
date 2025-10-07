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

        $user4 = User::where('name', 'Supervisor')->first();
        if (!$user4) {
            $user4 = User::create([
                'name' => 'Supervisor',
                'email' => 'supervisor@gmail.com',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'phone' => '123456789',
            ]);
        }
        $rol = Role::where('name', "Supervisor")->first();
        $user4->roles()->sync([$rol->id]);
        $branch = Branch::where('name', 'El Tigre')->first();
        $userBranch = new UserBranch();
        $userBranch->branch_id = $branch->id;
        $user4->userBranches()->save($userBranch);



        $user5 = User::where('name', 'Operador')->first();
        if (!$user5) {
            $user5 = User::create([
                'name' => 'Operador',
                'email' => 'operador@gmail.com',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'phone' => '123456789',
            ]);
        }
        $rol = Role::where('name', "Operador")->first();
        $user5->roles()->sync([$rol->id]);
        $branch = Branch::where('name', 'El Tigre')->first();
        $userBranch = new UserBranch();
        $userBranch->branch_id = $branch->id;
        $user5->userBranches()->save($userBranch);
    }
}
