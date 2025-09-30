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

    private $rol, $branch;

    public function __construct()
    {
        $this->rol = Role::where('name', "Cliente")->first();
        $this->branch = Branch::where('name', 'Cabimas')->first();
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(50)->create()->each(function ($user) {
            $user->roles()->sync([$this->rol->id]);
            $userBranch = new UserBranch();
            $userBranch->branch_id = $this->branch->id;
            $user->userBranches()->save($userBranch);
        });
    }
}
