<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement("SET time_zone = '-04:00'");
        $this->call(RolesSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(BranchSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(UserBranchSeeder::class);
        $this->call(BrandsSeeder::class);
        $this->call(TypeArticlesSeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(ConfigurationSeeder::class);
        $this->call(DailyRatesSeeder::class);
        $this->call(ModelVehicleSeeder::class);
        $this->call(MethodPaymentSeeder::class);

        // Nuevo
        $this->call(EquipmentSeeder::class);
        $this->call(FaultSeeder::class);
        $this->call(OwnerSeeder::class);
        $this->call(ProjectSeeder::class);
        $this->call(ServiceAreaSeeder::class);

        // $this->call(ServiceSeeder::class);
        // $this->call(CustomersSeeder::class);


        // \App\Models\User::factory(10)->create();
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
