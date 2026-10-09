<?php

namespace Tests\Feature\Faults;

use App\Models\Branch;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Cubre la persistencia del estado de los listados (filtros + página):
 * - Resumen de fallas y Equipos restauran la última URL guardada en sesión
 *   cuando se entra sin parámetros (p. ej. desde el menú).
 * - ?clear_filters=1 olvida el estado y deja el listado limpio.
 */
class ListStatePersistenceTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);
        $this->seed(PermissionsSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->branch = Branch::create([
            'name' => 'Sucursal Test',
            'email' => 'sucursal@test.com',
            'phone' => '0000000000',
        ]);
    }

    private function actingAsAdmin(): User
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'Admin')->firstOrFail();
        $user->assignRole($role);
        session(['branch' => $this->branch]);
        $this->actingAs($user);

        return $user;
    }

    public function test_resumen_fallas_restaura_ultimo_estado_guardado(): void
    {
        $this->actingAsAdmin();

        $this->get(route('admin.sucursal.faults.index', ['page' => 3, 'query' => 'motor']))
            ->assertOk();

        $response = $this->get(route('admin.sucursal.faults.index'));

        $response->assertRedirect(
            route('admin.sucursal.faults.index') . '?page=3&query=motor'
        );
    }

    public function test_resumen_fallas_sin_estado_guardado_no_redirige(): void
    {
        $this->actingAsAdmin();

        $this->get(route('admin.sucursal.faults.index'))->assertOk();
    }

    public function test_resumen_fallas_clear_filters_olvida_el_estado(): void
    {
        $this->actingAsAdmin();

        $this->get(route('admin.sucursal.faults.index', ['page' => 3]))
            ->assertOk();

        $this->get(route('admin.sucursal.faults.index', ['clear_filters' => 1]))
            ->assertRedirect(route('admin.sucursal.faults.index'));

        $this->get(route('admin.sucursal.faults.index'))->assertOk();
    }

    public function test_equipos_restaura_ultimo_estado_guardado(): void
    {
        $this->actingAsAdmin();

        $this->get(route('admin.sucursal.equipment.index', ['page' => 2, 'active' => '1']))
            ->assertOk();

        $response = $this->get(route('admin.sucursal.equipment.index'));

        $response->assertRedirect(
            route('admin.sucursal.equipment.index') . '?active=1&page=2'
        );
    }

    public function test_equipos_clear_filters_olvida_el_estado(): void
    {
        $this->actingAsAdmin();

        $this->get(route('admin.sucursal.equipment.index', ['internal_code' => 'EQ-9']))
            ->assertOk();

        $this->get(route('admin.sucursal.equipment.index', ['clear_filters' => 1]))
            ->assertRedirect(route('admin.sucursal.equipment.index'));

        $this->get(route('admin.sucursal.equipment.index'))->assertOk();
    }
}
