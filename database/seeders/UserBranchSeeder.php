<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\UserBranch;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::where('name', 'El Tigre')->first();
        $user = User::where('name', 'Admin')->first();

        $branchUser = UserBranch::where('user_id', $user->id)->where('branch_id', $branch->id)->first();

        if (!$branchUser) {
            $user->branches()->sync([$branch->id]);
        }
    }
}
