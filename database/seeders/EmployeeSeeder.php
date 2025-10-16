<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('name', 'El Tigre')->firstOrFail();

        $employees = $this->getEmployeeData($branch->id);

        foreach ($employees as $data) {
            $employee = $this->createOrGetEmployee($branch, $data);
            $user = $this->createUserWithRole($branch->id, $data);

            if ($user) {
                $employee->users()->syncWithoutDetaching([$user->id]);
            }
        }
    }

    private function createOrGetEmployee(Branch $branch, array $data)
    {
        $employeeData = Arr::except($data, ['role']);

        return $branch->employees()->firstOrCreate(
            ['identification_number' => $employeeData['identification_number']],
            $employeeData
        );
    }

    private function createUserWithRole(int $branchId, array $data): ?User
    {
        UserService::insertUserRole([
            'name' => "{$data['last_name']} {$data['first_name']}",
            'email' => $data['email'],
            'password' => 'password123',
            'phone' => $data['phone_number'],
            'role' => $data['role'],
            'branchId' => $branchId,
        ]);

        return User::where('email', $data['email'])->first();
    }


    private function getEmployeeData(int $branchId): array
    {
        return [
            [
                'branch_id' => $branchId,
                'identification_number' => '12345678',
                'first_name' => 'Pedro',
                'last_name' => 'Pozos',
                'email' => 'pedro.pozos@example.com',
                'phone_number' => '0414-1234567',
                'address' => 'Sector Producción, El Tigre',
                'executor' => true,
                'external' => true,
                'position' => 'Gerente de Producción',
                'role' => 'Supervisor',
            ],
            [
                'branch_id' => $branchId,
                'identification_number' => '87654321',
                'first_name' => 'Carla',
                'last_name' => 'Transversal',
                'email' => 'carla.transversal@example.com',
                'phone_number' => '0412-7654321',
                'address' => 'Zona Transversal, El Tigre',
                'executor' => true,
                'external' => false,
                'position' => 'Gerente de Ventas',
                'role' => 'Supervisor',
            ],
            [
                'branch_id' => $branchId,
                'identification_number' => '11223344',
                'first_name' => 'Luis',
                'last_name' => 'Logística',
                'email' => 'luis.logistica@example.com',
                'phone_number' => '0424-1122334',
                'address' => 'Base de Mantenimiento, El Tigre',
                'executor' => false,
                'external' => false,
                'position' => 'Operador de Maquinaria',
                'role' => 'Operador',
            ],
        ];
    }
}
