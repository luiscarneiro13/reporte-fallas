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

        //Admin Sucursal
        $rol = Role::where('name', "Admin Sucursal")->first();
        if (is_null($rol)) {
            Role::create(["name" => "Admin Sucursal", "guard_name" => "sanctum"]);
        }

        //Cajero
        $rol = Role::where('name', "Cajero")->first();
        if (is_null($rol)) {
            Role::create(["name" => "Cajero", "guard_name" => "sanctum"]);
        }

        //Vendedor
        $rol = Role::where('name', "Vendedor")->first();
        if (is_null($rol)) {
            Role::create(["name" => "Vendedor", "guard_name" => "sanctum"]);
        }

        //Cliente
        $rol = Role::where('name', "Cliente")->first();
        if (is_null($rol)) {
            Role::create(["name" => "Cliente", "guard_name" => "sanctum"]);
        }
    }
}
