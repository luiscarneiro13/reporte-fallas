<?php

namespace Tests\Feature\Faults;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\FaultStatus;
use App\Models\ServiceArea;
use App\Models\SparePartStatus;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Cubre las reglas especiales del rol Operador en v1/admin/fallas/create:
 * - El status de la falla queda fijo a FaultStatus::OPERATOR_STATUS_NAME, preseleccionado.
 * - El status de repuestos deja de ser obligatorio.
 * - La fecha del reporte se fuerza al día actual, sin importar lo que llegue en el POST.
 */
class FaultCreateOperatorTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;
    private Employee $employee;
    private Equipment $equipment;
    private ServiceArea $serviceArea;
    private SparePartStatus $sparePartStatus;

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

        $this->employee = $this->branch->employees()->create([
            'identification_number' => 'V-1000',
            'first_name' => 'Juan',
            'last_name' => 'Perez',
            'external' => 0,
        ]);

        $this->equipment = $this->branch->equipment()->create([
            'placa' => 'ABC123',
        ]);

        $this->serviceArea = $this->branch->serviceAreas()->create([
            'name' => 'Taller',
        ]);

        $this->sparePartStatus = $this->branch->sparePartStatus()->create([
            'name' => 'Por solicitar',
        ]);
    }

    private function actingAsOperador(): User
    {
        return $this->actingAsRole('Operador');
    }

    private function actingAsAdmin(): User
    {
        return $this->actingAsRole('Admin');
    }

    /**
     * Los roles del proyecto se seedean con guard_name "sanctum" (ver RolesSeeder),
     * mientras que la autenticación web usa el guard "web". assignRole('Nombre')
     * resuelve el guard por defecto del modelo y no encuentra el rol, así que se
     * busca la instancia de Role directamente para asignarla sin ese chequeo.
     */
    private function actingAsRole(string $roleName): User
    {
        $user = User::factory()->create();
        $role = Role::where('name', $roleName)->firstOrFail();
        $user->assignRole($role);
        session(['branch' => $this->branch]);
        $this->actingAs($user);

        return $user;
    }

    private function basePayload(array $overrides = []): array
    {
        return array_merge([
            'employee_reported_id' => $this->employee->id,
            'equipment_id' => $this->equipment->id,
            'service_area_id' => $this->serviceArea->id,
            'description' => 'Falla de prueba',
            'spare_part_status_id' => $this->sparePartStatus->id,
            'report_date' => now()->format('d-m-Y'),
        ], $overrides);
    }

    public function test_operador_ve_unicamente_el_status_por_programacion_interna_preseleccionado(): void
    {
        $this->actingAsOperador();

        $response = $this->get(route('admin.sucursal.faults.create'));

        $response->assertOk();
        $response->assertViewHas('isOperator', true);

        $faultStatus = $response->viewData('faultStatus');
        $this->assertCount(1, $faultStatus);
        $this->assertSame(FaultStatus::OPERATOR_STATUS_NAME, $faultStatus->first());

        $selectedId = $response->viewData('selectedFaultStatusId');
        $this->assertSame($faultStatus->keys()->first(), $selectedId);

        $this->assertDatabaseHas('fault_statuses', [
            'branch_id' => $this->branch->id,
            'name' => FaultStatus::OPERATOR_STATUS_NAME,
        ]);
    }

    public function test_operador_ve_fecha_de_reporte_precargada_con_hoy(): void
    {
        $this->actingAsOperador();

        $response = $this->get(route('admin.sucursal.faults.create'));

        $response->assertOk();
        $response->assertViewHas('reportDateValue', now()->toDateString());
    }

    public function test_no_operador_ve_todas_las_opciones_de_status_sin_preseleccion(): void
    {
        $this->branch->faultStatus()->create(['name' => 'En ejecución']);
        $this->branch->faultStatus()->create(['name' => 'En espera de repuesto']);

        $this->actingAsAdmin();

        $response = $this->get(route('admin.sucursal.faults.create'));

        $response->assertOk();
        $response->assertViewHas('isOperator', false);

        $faultStatus = $response->viewData('faultStatus');
        // Incluye el placeholder "Seleccione" + los 2 status creados.
        $this->assertCount(3, $faultStatus);
        $this->assertNull($response->viewData('selectedFaultStatusId'));
        $this->assertNull($response->viewData('reportDateValue'));
    }

    public function test_operador_puede_reportar_falla_sin_status_de_repuestos(): void
    {
        $this->actingAsOperador();

        $internalStatusId = FaultStatus::where('branch_id', $this->branch->id)
            ->where('name', FaultStatus::OPERATOR_STATUS_NAME)
            ->value('id');

        // No existe todavía porque nadie visitó create() en este test: se crea directo.
        if (!$internalStatusId) {
            $internalStatusId = $this->branch->faultStatus()
                ->create(['name' => FaultStatus::OPERATOR_STATUS_NAME])->id;
        }

        $payload = $this->basePayload([
            'fault_status_id' => $internalStatusId,
            'spare_part_status_id' => null,
        ]);
        unset($payload['spare_part_status_id']);

        $response = $this->post(route('admin.sucursal.faults.store'), $payload);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('faults', [
            'branch_id' => $this->branch->id,
            'fault_status_id' => $internalStatusId,
            'spare_part_status_id' => null,
            'report_date' => now()->toDateString(),
        ]);
    }

    public function test_operador_no_puede_forzar_una_fecha_de_reporte_distinta_a_hoy(): void
    {
        $this->actingAsOperador();

        $internalStatusId = $this->branch->faultStatus()
            ->create(['name' => FaultStatus::OPERATOR_STATUS_NAME])->id;

        $payload = $this->basePayload([
            'fault_status_id' => $internalStatusId,
            // Intento de manipular el campo deshabilitado con una fecha pasada.
            'report_date' => now()->subDays(10)->format('d-m-Y'),
        ]);

        $response = $this->post(route('admin.sucursal.faults.store'), $payload);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('faults', [
            'branch_id' => $this->branch->id,
            'report_date' => now()->toDateString(),
        ]);
    }

    public function test_operador_no_puede_forzar_un_status_de_falla_distinto(): void
    {
        $this->actingAsOperador();

        $otroStatus = $this->branch->faultStatus()->create(['name' => 'En ejecución']);

        $payload = $this->basePayload([
            'fault_status_id' => $otroStatus->id,
        ]);

        $response = $this->post(route('admin.sucursal.faults.store'), $payload);

        $response->assertSessionHasErrors('fault_status_id');
        $this->assertDatabaseMissing('faults', [
            'branch_id' => $this->branch->id,
            'fault_status_id' => $otroStatus->id,
        ]);
    }

    public function test_no_operador_sigue_requiriendo_status_de_repuestos(): void
    {
        $this->actingAsAdmin();

        $faultStatus = $this->branch->faultStatus()->create(['name' => 'En ejecución']);

        $payload = $this->basePayload([
            'fault_status_id' => $faultStatus->id,
        ]);
        unset($payload['spare_part_status_id']);

        $response = $this->post(route('admin.sucursal.faults.store'), $payload);

        $response->assertSessionHasErrors('spare_part_status_id');
    }
}
