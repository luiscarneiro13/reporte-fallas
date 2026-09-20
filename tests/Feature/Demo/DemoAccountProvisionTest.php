<?php

namespace Tests\Feature\Demo;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use App\Services\DemoProvisioningService;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Cubre el endpoint POST /api/v1/super-admin/provision-demo-accounts.
 *
 * El sistema está en producción con datos reales, así que además del camino
 * felíz se verifican las salvaguardas: token, idempotencia, no tocar la
 * sucursal real y no secuestrar usuarios existentes.
 */
class DemoAccountProvisionTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/api/v1/super-admin/provision-demo-accounts';
    private const TOKEN = 'token-de-prueba-123456';

    private Branch $realBranch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);
        $this->seed(PermissionsSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        // Se replica el escenario de producción: existe una sucursal real y es
        // la única habilitada para enviar correo. Sin ella, la sucursal demo
        // podría tomar el ID 1 y la salvaguarda abortaría (comportamiento
        // correcto, pero dependiente del autoincremento y por tanto del orden
        // en que corren los tests).
        $this->realBranch = Branch::create([
            'name' => 'Sucursal Real de Producción',
            'email' => 'produccion@example.com',
            'phone' => '0000000000',
        ]);

        config()->set('demo.provision_token', self::TOKEN);
        config()->set('demo.default_password', null);
        config()->set('demo.mail_enabled_branch_ids', [$this->realBranch->id]);
    }

    private function provision(array $payload = [])
    {
        return $this->withHeader('X-Demo-Provision-Token', self::TOKEN)
            ->postJson(self::URL, $payload);
    }

    public function test_responde_503_si_el_token_no_esta_configurado(): void
    {
        config()->set('demo.provision_token', null);

        $this->postJson(self::URL)
            ->assertStatus(503)
            ->assertJsonPath('error_code', 'PROVISION_DISABLED');

        // Solo queda la sucursal real creada en setUp: no se provisionó nada.
        $this->assertDatabaseCount('branches', 1);
    }

    public function test_rechaza_peticion_sin_token(): void
    {
        $this->postJson(self::URL)
            ->assertStatus(401)
            ->assertJsonPath('error_code', 'INVALID_PROVISION_TOKEN');

        // Solo queda la sucursal real creada en setUp: no se provisionó nada.
        $this->assertDatabaseCount('branches', 1);
    }

    public function test_rechaza_token_invalido(): void
    {
        $this->withHeader('X-Demo-Provision-Token', 'token-equivocado')
            ->postJson(self::URL)
            ->assertStatus(401);

        // Solo queda la sucursal real creada en setUp: no se provisionó nada.
        $this->assertDatabaseCount('branches', 1);
    }

    public function test_acepta_el_token_por_body(): void
    {
        $this->postJson(self::URL, ['token' => self::TOKEN])->assertOk();
    }

    public function test_el_endpoint_no_responde_por_get(): void
    {
        $this->getJson(self::URL)->assertStatus(405);
    }

    public function test_crea_las_dos_sucursales_con_sus_cuatro_cuentas(): void
    {
        $response = $this->provision()->assertOk();

        $response->assertJsonPath('data.all_ok', true);

        foreach (DemoProvisioningService::BRANCHES as $definition) {
            $this->assertDatabaseHas('branches', ['name' => $definition['name']]);
        }

        foreach (['ios.supervisor', 'ios.operador', 'android.supervisor', 'android.operador'] as $prefix) {
            $this->assertDatabaseHas('users', ['email' => $prefix . '@example.com']);
        }

        $this->assertDatabaseCount('users', 4);
    }

    public function test_las_cuentas_quedan_verificadas_con_rol_y_una_sola_sucursal(): void
    {
        $this->provision()->assertOk();

        $iosBranch = Branch::where('name', 'Demo Review iOS')->firstOrFail();

        $supervisor = User::where('email', 'ios.supervisor@example.com')->firstOrFail();
        $this->assertNotNull($supervisor->email_verified_at, 'Debe estar pre-verificada: el login API exige email verificado.');
        $this->assertTrue($supervisor->hasRole('Supervisor', 'sanctum'));

        // BranchHelper usa branches()->first(): más de una fila rompería el aislamiento.
        $this->assertSame(1, $supervisor->userBranches()->count());
        $this->assertSame($iosBranch->id, $supervisor->userBranches()->first()->branch_id);

        $operador = User::where('email', 'ios.operador@example.com')->firstOrFail();
        $this->assertTrue($operador->hasRole('Operador', 'sanctum'));
        $this->assertSame(1, $operador->userBranches()->count());

        // Sin empleado asociado el login devuelve OPERATOR_WITHOUT_EMPLOYEE.
        $this->assertSame(1, $operador->employees()->count());
        $this->assertSame($iosBranch->id, $operador->employees()->first()->branch_id);
    }

    public function test_los_dropdowns_de_crear_falla_quedan_poblados(): void
    {
        $response = $this->provision()->assertOk();

        foreach (['ios', 'android'] as $key) {
            $dropdowns = $response->json("data.branches.{$key}.dropdowns");

            $this->assertNotEmpty($dropdowns);

            foreach ($dropdowns as $name => $count) {
                $this->assertGreaterThan(0, $count, "El dropdown '{$name}' de {$key} quedó vacío.");
            }

            $this->assertTrue($response->json("data.branches.{$key}.dropdowns_ok"));
        }
    }

    public function test_cubre_las_tres_combinaciones_de_flags_de_empleados(): void
    {
        $this->provision()->assertOk();

        $branchId = Branch::where('name', 'Demo Review iOS')->value('id');

        // employee_reported → external = 0
        $this->assertGreaterThan(0, Employee::where('branch_id', $branchId)->where('external', 0)->count());
        // executors_internal → executor = 1, external = 0
        $this->assertGreaterThan(0, Employee::where('branch_id', $branchId)->where('executor', 1)->where('external', 0)->count());
        // executors_external → executor = 1, external = 1
        $this->assertGreaterThan(0, Employee::where('branch_id', $branchId)->where('executor', 1)->where('external', 1)->count());
    }

    public function test_crea_equipos_con_uuid_y_vinculados_al_proyecto(): void
    {
        $this->provision()->assertOk();

        $branchId = Branch::where('name', 'Demo Review iOS')->value('id');
        $equipment = \App\Models\Equipment::where('branch_id', $branchId)->get();

        $this->assertCount(3, $equipment);

        foreach ($equipment as $item) {
            $this->assertNotEmpty($item->uuid, 'EquipmentObserver debe asignar uuid al crear.');
            $this->assertSame(1, $item->projects()->count(), 'Debe quedar vinculado al proyecto demo.');
        }
    }

    public function test_crea_fallas_de_ejemplo_sin_enviar_correo_ni_push(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $this->provision()->assertOk();

        $branchId = Branch::where('name', 'Demo Review iOS')->value('id');
        // 3 fallas: 2 del reportante + 1 del operador (esta última es necesaria
        // porque el rol Operador solo ve las fallas que él reportó).
        $this->assertSame(3, \App\Models\Fault::where('branch_id', $branchId)->count());

        \Illuminate\Support\Facades\Mail::assertNothingSent();
    }

    public function test_es_idempotente_al_ejecutarse_dos_veces(): void
    {
        $this->provision()->assertOk();

        $snapshot = [
            'branches' => Branch::count(),
            'users' => User::count(),
            'employees' => Employee::count(),
            'equipment' => \App\Models\Equipment::count(),
            'faults' => \App\Models\Fault::count(),
            'fault_statuses' => \App\Models\FaultStatus::count(),
            'service_areas' => \App\Models\ServiceArea::count(),
            'spare_part_statuses' => \App\Models\SparePartStatus::count(),
            'projects' => \App\Models\Project::count(),
            'customers' => \App\Models\Customer::count(),
            'divisions' => \App\Models\Division::count(),
            'equipment_types' => \App\Models\EquipmentType::count(),
            'user_branch' => \App\Models\UserBranch::count(),
        ];

        $this->provision()->assertOk();
        $this->provision()->assertOk();

        foreach ($snapshot as $table => $expected) {
            $this->assertSame($expected, match ($table) {
                'branches' => Branch::count(),
                'users' => User::count(),
                'employees' => Employee::count(),
                'equipment' => \App\Models\Equipment::count(),
                'faults' => \App\Models\Fault::count(),
                'fault_statuses' => \App\Models\FaultStatus::count(),
                'service_areas' => \App\Models\ServiceArea::count(),
                'spare_part_statuses' => \App\Models\SparePartStatus::count(),
                'projects' => \App\Models\Project::count(),
                'customers' => \App\Models\Customer::count(),
                'divisions' => \App\Models\Division::count(),
                'equipment_types' => \App\Models\EquipmentType::count(),
                'user_branch' => \App\Models\UserBranch::count(),
            }, "La tabla '{$table}' se duplicó al reejecutar el provisioning.");
        }
    }

    public function test_no_cambia_la_contrasena_de_una_cuenta_existente_por_defecto(): void
    {
        $this->provision(['password' => 'ClaveInicial123'])->assertOk();

        $original = User::where('email', 'ios.supervisor@example.com')->value('password');

        $response = $this->provision(['password' => 'ClaveNueva456'])->assertOk();

        $this->assertSame($original, User::where('email', 'ios.supervisor@example.com')->value('password'));

        // La respuesta lo declara explícitamente.
        $users = collect($response->json('data.branches.ios.users'));
        $this->assertFalse($users->firstWhere('role', 'Supervisor')['created']);
        $this->assertNull($users->firstWhere('role', 'Supervisor')['password']);
    }

    public function test_reset_passwords_si_cambia_la_contrasena(): void
    {
        $this->provision(['password' => 'ClaveInicial123'])->assertOk();

        $this->provision(['password' => 'ClaveNueva456', 'reset_passwords' => true])->assertOk();

        $user = User::where('email', 'ios.supervisor@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('ClaveNueva456', $user->password));
    }

    public function test_devuelve_la_contrasena_generada_cuando_no_se_envia_ninguna(): void
    {
        $response = $this->provision()->assertOk();

        $password = $response->json('data.branches.ios.users.0.password');

        $this->assertNotEmpty($password);
        $this->assertGreaterThanOrEqual(8, strlen($password));

        $user = User::where('email', 'ios.supervisor@example.com')->firstOrFail();
        $this->assertTrue(Hash::check($password, $user->password));
    }

    public function test_el_filtro_only_provisiona_una_sola_tienda(): void
    {
        $this->provision(['only' => ['ios']])->assertOk();

        $this->assertDatabaseHas('branches', ['name' => 'Demo Review iOS']);
        $this->assertDatabaseMissing('branches', ['name' => 'Demo Review Android']);
        $this->assertDatabaseCount('users', 2);
    }

    public function test_rechaza_un_valor_invalido_en_only(): void
    {
        $this->withHeader('X-Demo-Provision-Token', self::TOKEN)
            ->postJson(self::URL, ['only' => ['windows-phone']])
            ->assertStatus(422);

        // Solo queda la sucursal real creada en setUp: no se provisionó nada.
        $this->assertDatabaseCount('branches', 1);
    }

    public function test_aborta_si_la_sucursal_demo_resolviera_a_una_sucursal_de_produccion(): void
    {
        // Simula el peor caso: que el ID de la sucursal demo esté declarado como
        // productivo (por ejemplo si alguien renombró la sucursal real).
        $branch = Branch::create([
            'name' => 'Demo Review iOS',
            'email' => 'x@example.com',
            'phone' => '0',
        ]);
        config()->set('demo.mail_enabled_branch_ids', [$branch->id]);

        $this->provision(['only' => ['ios']])
            ->assertStatus(409)
            ->assertJsonPath('error_code', 'PROVISION_ABORTED');

        // No creó usuarios ni datos dentro de la sucursal real.
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('employees', 0);
    }

    public function test_aborta_si_el_email_demo_ya_pertenece_a_otra_sucursal(): void
    {
        $otra = Branch::create([
            'name' => 'Sucursal Real',
            'email' => 'real@example.com',
            'phone' => '0',
        ]);

        $existing = User::create([
            'name' => 'Usuario Real',
            'email' => 'ios.supervisor@example.com',
            'password' => Hash::make('secreto-original'),
            'email_verified_at' => now(),
        ]);
        $existing->userBranches()->create(['branch_id' => $otra->id]);

        $this->provision(['only' => ['ios']])
            ->assertStatus(409)
            ->assertJsonPath('error_code', 'PROVISION_ABORTED');

        // El usuario ajeno quedó intacto.
        $existing->refresh();
        $this->assertTrue(Hash::check('secreto-original', $existing->password));
        $this->assertSame(1, $existing->userBranches()->count());
        $this->assertSame($otra->id, $existing->userBranches()->first()->branch_id);
    }

    /**
     * El rol Operador solo ve las fallas que él mismo reportó (FaultController
     * filtra por reported_by_id). Sin una falla suya, el revisor de la tienda
     * vería el listado vacío.
     */
    public function test_el_operador_tiene_al_menos_una_falla_propia(): void
    {
        $this->provision()->assertOk();

        foreach (['Demo Review iOS', 'Demo Review Android'] as $branchName) {
            $branchId = Branch::where('name', $branchName)->value('id');

            $operador = User::where('email', ($branchName === 'Demo Review iOS' ? 'ios' : 'android') . '.operador@example.com')
                ->firstOrFail();
            $empleadoId = $operador->employees()->first()->id;

            $propias = \App\Models\Fault::where('branch_id', $branchId)
                ->where('employee_reported_id', $empleadoId)
                ->count();

            $this->assertGreaterThan(
                0,
                $propias,
                "El operador de '{$branchName}' no tiene fallas propias: vería el listado vacío."
            );
        }
    }

    public function test_el_token_no_se_acepta_por_query_string(): void
    {
        // La query string queda registrada en los access logs del hosting.
        $this->postJson(self::URL . '?token=' . self::TOKEN)
            ->assertStatus(401)
            ->assertJsonPath('error_code', 'INVALID_PROVISION_TOKEN');

        $this->assertDatabaseCount('branches', 1);
    }

    public function test_rechaza_una_contrasena_debil(): void
    {
        $this->withHeader('X-Demo-Provision-Token', self::TOKEN)
            ->postJson(self::URL, ['password' => 'corta12'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');

        $this->assertDatabaseCount('branches', 1);
    }

    public function test_no_filtra_detalles_internos_en_un_error_inesperado(): void
    {
        // Se fuerza un fallo interno quitando el rol que el servicio necesita.
        \Spatie\Permission\Models\Role::where('name', 'Supervisor')->delete();

        $response = $this->provision(['only' => ['ios']])->assertStatus(409);

        // Es una salvaguarda controlada, no una excepción cruda con SQL.
        $response->assertJsonPath('error_code', 'PROVISION_ABORTED');
        $this->assertStringNotContainsString('select', strtolower($response->json('message')));
    }

    public function test_aborta_si_el_email_demo_pertenece_a_una_cuenta_con_otro_rol(): void
    {
        // Cuenta real sin sucursal asignada pero con rol Admin: roles()->sync()
        // se lo borraría, así que debe abortar.
        $existing = User::create([
            'name' => 'Admin Real',
            'email' => 'ios.supervisor@example.com',
            'password' => Hash::make('secreto-original'),
            'email_verified_at' => now(),
        ]);
        $existing->assignRole(
            \Spatie\Permission\Models\Role::where('name', 'Admin')->where('guard_name', 'sanctum')->firstOrFail()
        );

        $this->provision(['only' => ['ios']])
            ->assertStatus(409)
            ->assertJsonPath('error_code', 'PROVISION_ABORTED');

        $existing->refresh();
        $this->assertTrue($existing->hasRole('Admin', 'sanctum'), 'El rol original no debe modificarse.');
    }

    public function test_no_modifica_la_contrasena_de_una_cuenta_ajena_ni_con_reset_passwords(): void
    {
        $otra = Branch::create([
            'name' => 'Otra Sucursal',
            'email' => 'otra@example.com',
            'phone' => '0',
        ]);

        $existing = User::create([
            'name' => 'Usuario Real',
            'email' => 'ios.supervisor@example.com',
            'password' => Hash::make('secreto-original'),
            'email_verified_at' => now(),
        ]);
        $existing->userBranches()->create(['branch_id' => $otra->id]);

        $this->provision(['only' => ['ios'], 'password' => 'ClaveNueva12345', 'reset_passwords' => true])
            ->assertStatus(409);

        // La verificación ocurre antes de escribir: la contraseña sigue intacta.
        $existing->refresh();
        $this->assertTrue(Hash::check('secreto-original', $existing->password));
    }

    public function test_las_sucursales_demo_no_estan_habilitadas_para_enviar_correo(): void
    {
        $response = $this->provision()->assertOk();

        foreach (['ios', 'android'] as $key) {
            $this->assertFalse(
                $response->json("data.branches.{$key}.mail_enabled"),
                "La sucursal demo {$key} no debe poder enviar correos."
            );
        }
    }
}
