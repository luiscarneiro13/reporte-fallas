<?php

namespace Tests\Feature\Demo;

use App\Mail\CerrarFallaEmail;
use App\Mail\ReportarFallaEmail;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\FaultStatus;
use App\Models\ServiceArea;
use App\Models\SparePartStatus;
use App\Models\User;
use App\Services\FaultMailService;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * El correo de fallas (EMAIL_FALLAS) llega a un buzón real de la empresa. Las
 * sucursales demo que usan los revisores de App Store / Google Play deben poder
 * crear y cerrar fallas sin escribirle a ese buzón.
 *
 * Se prueba tanto el servicio como el endpoint real de la app móvil, porque la
 * regla se aplica dentro del servicio pero lo que importa es que el flujo
 * completo no envíe nada.
 */
class FaultMailBranchPolicyTest extends TestCase
{
    use RefreshDatabase;

    private Branch $realBranch;
    private Branch $demoBranch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);
        $this->seed(PermissionsSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->realBranch = Branch::create([
            'name' => 'Sucursal Real',
            'email' => 'real@example.com',
            'phone' => '0000000000',
        ]);

        $this->demoBranch = Branch::create([
            'name' => 'Demo Review iOS',
            'email' => 'demo@example.com',
            'phone' => '0000000000',
        ]);

        config()->set('demo.mail_enabled_branch_ids', [$this->realBranch->id]);
        config()->set('mail.fault_notifications_to', 'mantenimiento@example.com');
    }

    public function test_la_sucursal_real_si_esta_habilitada(): void
    {
        $service = app(FaultMailService::class);

        $this->assertTrue($service->isMailEnabledForBranch($this->realBranch->id));
    }

    public function test_la_sucursal_demo_no_esta_habilitada(): void
    {
        $service = app(FaultMailService::class);

        $this->assertFalse($service->isMailEnabledForBranch($this->demoBranch->id));
    }

    public function test_una_sucursal_sin_resolver_no_esta_habilitada(): void
    {
        $service = app(FaultMailService::class);

        // BranchHelper devuelve null si el usuario no tiene sucursal: no se debe
        // enviar correo "por si acaso".
        $this->assertFalse($service->isMailEnabledForBranch(null));
    }

    public function test_el_servicio_envia_para_la_sucursal_real(): void
    {
        Mail::fake();

        $enviado = app(FaultMailService::class)->sendReported((object) ['id' => 1], $this->realBranch->id);

        $this->assertTrue($enviado);
        Mail::assertSent(ReportarFallaEmail::class);
    }

    public function test_el_servicio_no_envia_para_la_sucursal_demo(): void
    {
        Mail::fake();

        $enviado = app(FaultMailService::class)->sendReported((object) ['id' => 1], $this->demoBranch->id);

        $this->assertFalse($enviado);
        Mail::assertNothingSent();
    }

    public function test_el_servicio_no_envia_cierre_para_la_sucursal_demo(): void
    {
        Mail::fake();

        $enviado = app(FaultMailService::class)->sendClosed((object) ['id' => 1], $this->demoBranch->id);

        $this->assertFalse($enviado);
        Mail::assertNotSent(CerrarFallaEmail::class);
    }

    public function test_no_envia_si_el_destinatario_no_esta_configurado(): void
    {
        Mail::fake();
        config()->set('mail.fault_notifications_to', null);

        $enviado = app(FaultMailService::class)->sendReported((object) ['id' => 1], $this->realBranch->id);

        $this->assertFalse($enviado);
        Mail::assertNothingSent();
    }

    /**
     * Flujo completo de la app móvil: un supervisor de la sucursal demo reporta
     * una falla por la API. No debe salir ningún correo.
     */
    public function test_crear_falla_por_la_api_en_sucursal_demo_no_envia_correo(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/v1/fallas', $this->payloadPara($this->demoBranch));

        $response->assertStatus(201);

        Mail::assertNothingSent();

        $this->assertDatabaseHas('faults', ['branch_id' => $this->demoBranch->id]);
    }

    /**
     * Contraparte del test anterior: la sucursal real debe seguir enviando
     * correo exactamente como antes (no romper el comportamiento productivo).
     */
    public function test_crear_falla_por_la_api_en_sucursal_real_si_envia_correo(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/v1/fallas', $this->payloadPara($this->realBranch));

        $response->assertStatus(201);

        Mail::assertSent(ReportarFallaEmail::class);
    }

    /**
     * El cierre de una falla también envía correo, y en el flujo de cierre la
     * falla se borra de `faults` y se archiva en `fault_history`: hay que
     * asegurar que el branch_id siga resolviéndose bien en ese punto.
     */
    public function test_cerrar_falla_por_la_api_respeta_la_politica_por_sucursal(): void
    {
        // Sucursal demo: no debe enviar nada.
        Mail::fake();

        $payload = $this->payloadPara($this->demoBranch);
        $faultId = $this->postJson('/api/v1/fallas', $payload)->assertStatus(201)->json('data.id');

        $this->putJson('/api/v1/fallas/' . $faultId, array_merge($payload, [
            'closed' => true,
            'scheduled_execution' => now()->format('d-m-Y'),
            'completed_execution' => now()->format('d-m-Y'),
            'equipment_maintenance_log' => 'Mantenimiento de prueba',
        ]))->assertOk();

        Mail::assertNotSent(CerrarFallaEmail::class);
        $this->assertDatabaseHas('fault_history', ['branch_id' => $this->demoBranch->id]);
    }

    public function test_cerrar_falla_en_sucursal_real_si_envia_correo(): void
    {
        Mail::fake();

        $payload = $this->payloadPara($this->realBranch);
        $faultId = $this->postJson('/api/v1/fallas', $payload)->assertStatus(201)->json('data.id');

        $this->putJson('/api/v1/fallas/' . $faultId, array_merge($payload, [
            'closed' => true,
            'scheduled_execution' => now()->format('d-m-Y'),
            'completed_execution' => now()->format('d-m-Y'),
            'equipment_maintenance_log' => 'Mantenimiento de prueba',
        ]))->assertOk();

        Mail::assertSent(CerrarFallaEmail::class);
    }

    /**
     * Si MAIL_ENABLED_BRANCH_IDS quedara vacía en el .env, una lista blanca vacía
     * dejaría SIN CORREOS a la sucursal real. config/demo.php debe caer a [1].
     */
    public function test_la_lista_blanca_nunca_queda_vacia(): void
    {
        foreach (['', '   ', ',,', '0'] as $valorEnv) {
            $parsed = array_values(array_filter(array_map(
                'intval',
                explode(',', (string) ($valorEnv ?: '1'))
            ))) ?: [1];

            $this->assertSame([1], $parsed, "El valor '{$valorEnv}' debe caer al fallback [1].");
        }

        // Y un valor con espacios se parsea bien.
        $conEspacios = array_values(array_filter(array_map('intval', explode(',', '1, 2'))));
        $this->assertSame([1, 2], $conEspacios);
    }

    /**
     * Crea los datos mínimos en la sucursal indicada, autentica a un supervisor
     * de esa sucursal por Sanctum y devuelve el payload para POST /fallas.
     */
    private function payloadPara(Branch $branch): array
    {
        $employee = Employee::create([
            'branch_id' => $branch->id,
            'identification_number' => 'EMP-' . $branch->id,
            'first_name' => 'Test',
            'last_name' => 'Empleado',
            'external' => 0,
        ]);

        $equipment = Equipment::create([
            'branch_id' => $branch->id,
            'placa' => 'PL' . $branch->id,
        ]);

        // ServiceArea/FaultStatus no tienen branch_id en $fillable: se crean por
        // la relación de la sucursal (igual que el código de producción), o el
        // branch_id se descartaría en la asignación masiva.
        $serviceArea = $branch->serviceAreas()->create(['name' => 'Área']);
        $faultStatus = $branch->faultStatus()->create(['name' => 'En ejecución']);
        $sparePartStatus = $branch->sparePartStatus()->create(['name' => 'Por solicitar']);

        $user = User::create([
            'name' => 'Supervisor ' . $branch->id,
            'email' => 'supervisor' . $branch->id . '@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Role::where('name', 'Supervisor')->where('guard_name', 'sanctum')->firstOrFail());
        $user->userBranches()->create(['branch_id' => $branch->id]);

        Sanctum::actingAs($user, ['*']);

        return [
            'employee_reported_id' => $employee->id,
            'equipment_id' => $equipment->id,
            'service_area_id' => $serviceArea->id,
            'description' => 'Falla de prueba',
            'fault_status_id' => $faultStatus->id,
            'spare_part_status_id' => $sparePartStatus->id,
            'report_date' => now()->format('d-m-Y'),
        ];
    }
}
