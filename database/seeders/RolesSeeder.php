<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Super Admin
        $rol = Role::where('name', "Super Admin")->first();
        if (is_null($rol)) {
            Role::create(["name" => "Super Admin", "guard_name" => "sanctum"]);
        }

        //Admin
        $rol = Role::where('name', "Admin")->first();
        if (is_null($rol)) {
            Role::create(["name" => "Admin", "guard_name" => "sanctum"]);
        }

        //Supervisor
        $rol = Role::where('name', "Supervisor")->first();
        if (is_null($rol)) {
            Role::create(["name" => "Supervisor", "guard_name" => "sanctum"]);
        }

        //Operador
        $rol = Role::where('name', "Operador")->first();
        if (is_null($rol)) {
            Role::create(["name" => "Operador", "guard_name" => "sanctum"]);
        }

    }
}
