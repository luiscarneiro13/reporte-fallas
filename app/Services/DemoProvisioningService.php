<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Models\Fault;
use App\Models\FaultStatus;
use App\Models\ModelVehicle;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Crea (o repara) las sucursales y cuentas ficticias que usan los revisores de
 * App Store y Google Play. Ver docs/demo-accounts.md.
 *
 * Existe como servicio disparable por HTTP y no como seeder porque el hosting
 * es compartido y no hay acceso a artisan/CLI — mismo motivo que
 * EquipmentUuidBackfillController.
 *
 * Garantías de diseño (el sistema está en producción con datos reales):
 *
 * 1. IDEMPOTENTE: todo se resuelve con firstOrCreate sobre claves naturales.
 *    Re-ejecutarlo no duplica ni sobrescribe nada.
 * 2. NO TOCA LA SUCURSAL REAL: aborta si una sucursal demo resolviera a un ID
 *    que está en la lista blanca de correos (config demo.mail_enabled_branch_ids,
 *    o sea la sucursal 1).
 * 3. NO SECUESTRA USUARIOS: si uno de los emails demo ya existe pero está
 *    vinculado a otra sucursal, aborta sin modificarlo.
 * 4. UNA SOLA SUCURSAL POR USUARIO: BranchHelper::getBranchId() usa
 *    branches()->first(), así que un segundo user_branch rompería el
 *    aislamiento. Se verifica explícitamente.
 * 5. NO ENVÍA CORREOS NI PUSH: las fallas de ejemplo se crean con Eloquent
 *    directo, sin pasar por los controladores que notifican.
 */
class DemoProvisioningService
{
    /**
     * Sucursales demo. La clave `name` es la identidad: si se cambia, la
     * siguiente ejecución crearía una sucursal nueva en vez de reusar la
     * existente.
     */
    public const BRANCHES = [
        'ios' => [
            'name' => 'Demo Review iOS',
            // `platform` es explícito y no derivado de `prefix`: forma parte de
            // los emails de las cuentas y no debe cambiar por accidente.
            'platform' => 'ios',
            'prefix' => 'DEMO-IOS',
            'plate_prefix' => 'DMOI',
            'store' => 'App Store (Apple)',
        ],
        'android' => [
            'name' => 'Demo Review Android',
            'platform' => 'android',
            'prefix' => 'DEMO-AND',
            'plate_prefix' => 'DMOA',
            'store' => 'Google Play',
        ],
    ];

    /** Cuentas por sucursal: sufijo de email => rol. */
    private const USERS = [
        'supervisor' => 'Supervisor',
        'operador' => 'Operador',
    ];

    private const EMAIL_DOMAIN = 'example.com';

    /**
     * @param  array<string>  $only  Claves de BRANCHES a procesar (vacío = todas).
     * @return array<string, mixed> Resumen para la respuesta HTTP.
     */
    public function provision(?string $password = null, bool $resetPasswords = false, array $only = []): array
    {
        $this->assertRolesExist();

        $targets = empty($only)
            ? self::BRANCHES
            : array_intersect_key(self::BRANCHES, array_flip($only));

        if (empty($targets)) {
            throw new RuntimeException('No hay sucursales demo que coincidan con el filtro recibido.');
        }

        $result = [];

        foreach ($targets as $key => $definition) {
            // Una transacción por sucursal: si una falla, la otra ya aplicada
            // queda consistente y se puede reintentar sin efectos a medias.
            $result[$key] = DB::transaction(
                fn () => $this->provisionBranch($key, $definition, $password, $resetPasswords)
            );
        }

        return $result;
    }

    private function provisionBranch(string $key, array $definition, ?string $password, bool $resetPasswords): array
    {
        $branch = Branch::firstOrCreate(
            ['name' => $definition['name']],
            [
                'description' => 'Sucursal ficticia para la revisión de ' . $definition['store'] . '. No usar para operación real.',
                'email' => $key . '.demo@' . self::EMAIL_DOMAIN,
                'phone' => '0000000000',
                'rif' => 'J-00000000-0',
                'address' => 'Dirección ficticia de pruebas',
            ]
        );

        $this->assertNotProductionBranch($branch);

        $catalogs = $this->provisionCatalogs($branch);
        $project = $this->provisionProjectChain($branch, $definition);
        $employees = $this->provisionEmployees($branch, $definition);
        $equipment = $this->provisionEquipment($branch, $definition, $project, $catalogs['equipment_types']);
        $users = $this->provisionUsers($branch, $definition, $employees, $password, $resetPasswords);
        $faults = $this->provisionSampleFaults($branch, $definition, $employees, $equipment, $catalogs);

        return [
            'store' => $definition['store'],
            'branch' => ['id' => $branch->id, 'name' => $branch->name],
            'users' => $users,
            'counts' => [
                'fault_statuses' => $catalogs['fault_statuses']->count(),
                'spare_part_statuses' => $catalogs['spare_part_statuses']->count(),
                'service_areas' => $catalogs['service_areas']->count(),
                'equipment_types' => $catalogs['equipment_types']->count(),
                'employees' => count($employees),
                'equipment' => count($equipment),
                'projects' => 1,
                'sample_faults' => $faults,
            ],
        ];
    }

    /**
     * Salvaguarda principal: nunca operar sobre una sucursal real. La lista
     * blanca de correos (por defecto [1]) es justamente la lista de sucursales
     * productivas, así que sirve de referencia para este chequeo.
     */
    private function assertNotProductionBranch(Branch $branch): void
    {
        $productionIds = config('demo.mail_enabled_branch_ids', [1]);

        if (in_array((int) $branch->id, array_map('intval', $productionIds), true)) {
            throw new RuntimeException(
                "Abortado: la sucursal demo '{$branch->name}' resolvió al ID {$branch->id}, " .
                'que está declarado como sucursal de producción en config/demo.php. ' .
                'Revisar nombres de sucursales antes de reintentar.'
            );
        }
    }

    private function assertRolesExist(): void
    {
        foreach (array_unique(array_values(self::USERS)) as $roleName) {
            $exists = Role::where('name', $roleName)->where('guard_name', 'sanctum')->exists();

            if (!$exists) {
                throw new RuntimeException(
                    "El rol '{$roleName}' (guard sanctum) no existe. Ejecutar los seeders de roles antes de provisionar."
                );
            }
        }
    }

    /**
     * Catálogos que alimentan los dropdowns de "crear falla" y "crear equipo".
     * Ninguno puede quedar vacío o el formulario de la app queda inusable.
     *
     * fault_statuses incluye obligatoriamente FaultStatus::OPERATOR_STATUS_NAME:
     * es el único status que ve el rol Operador y el backend lo exige al validar.
     */
    private function provisionCatalogs(Branch $branch): array
    {
        $faultStatusNames = [
            FaultStatus::OPERATOR_STATUS_NAME,
            'En espera de repuesto',
            'En ejecución',
            'En espera por coordinación con el cliente',
        ];

        $faultStatuses = collect($faultStatusNames)->map(
            fn ($name) => $branch->faultStatus()->firstOrCreate(['name' => $name])
        );

        $sparePartStatuses = collect(['Por solicitar', 'Solicitado', 'Recibido'])->map(
            fn ($name) => $branch->sparePartStatus()->firstOrCreate(['name' => $name])
        );

        $serviceAreas = collect(['Taller Demo', 'Patio Demo', 'Operaciones Demo'])->map(
            fn ($name) => $branch->serviceAreas()->firstOrCreate(['name' => $name])
        );

        // EquipmentType no tiene relación en Branch: se crea con branch_id explícito.
        $equipmentTypes = collect(['Camión Demo', 'Montacargas Demo', 'Planta Eléctrica Demo'])->map(
            fn ($name) => EquipmentType::firstOrCreate(['branch_id' => $branch->id, 'name' => $name])
        );

        // Marcas y modelos: necesarios para el formulario de creación de equipos.
        collect(['Marca Demo Uno', 'Marca Demo Dos'])->each(
            fn ($name) => Brand::firstOrCreate(['branch_id' => $branch->id, 'name' => $name])
        );

        collect(['Modelo Demo A', 'Modelo Demo B'])->each(
            fn ($name) => ModelVehicle::firstOrCreate(['branch_id' => $branch->id, 'name' => $name])
        );

        return [
            'fault_statuses' => $faultStatuses,
            'spare_part_statuses' => $sparePartStatuses,
            'service_areas' => $serviceAreas,
            'equipment_types' => $equipmentTypes,
        ];
    }

    private function provisionProjectChain(Branch $branch, array $definition)
    {
        $customer = Customer::firstOrCreate(
            ['branch_id' => $branch->id, 'rif' => $definition['prefix'] . '-CLI'],
            [
                'name' => 'Cliente Demo ' . $definition['prefix'],
                'address' => 'Dirección ficticia',
                'phone' => '0000000000',
                'email' => 'cliente.' . Str::lower($definition['prefix']) . '@' . self::EMAIL_DOMAIN,
            ]
        );

        $division = $branch->divisions()->firstOrCreate(
            ['name' => 'División Demo'],
            ['description' => 'División ficticia de pruebas']
        );

        return $branch->projects()->firstOrCreate(
            ['name' => 'Proyecto Demo ' . $definition['prefix']],
            [
                'customer_id' => $customer->id,
                'division_id' => $division->id,
                'contract_number' => $definition['prefix'] . '-0001',
                'description' => 'Proyecto ficticio de pruebas',
                'geographic_area' => 'Área ficticia',
            ]
        );
    }

    /**
     * Los tres dropdowns de personas de "crear falla" filtran la MISMA tabla con
     * flags distintos (ver FaultService):
     *   employee_reported   → external = 0
     *   executors_internal  → executor = 1 y external = 0
     *   executors_external  → executor = 1 y external = 1
     * Por eso hacen falta empleados que cubran las tres combinaciones, o algún
     * dropdown quedaría vacío.
     *
     * @return array<string, Employee>
     */
    private function provisionEmployees(Branch $branch, array $definition): array
    {
        $prefix = $definition['prefix'];

        $definitions = [
            // Empleado del usuario Operador: se vincula vía employee_users.
            'operador' => ['001', 'Olga', 'Operadora Demo', 0, 0, 'Operador de campo'],
            // Reportante adicional, para que el dropdown tenga más de una opción.
            'reportante' => ['002', 'Ramón', 'Reportante Demo', 0, 0, 'Analista'],
            // Ejecutor interno.
            'ejecutor_interno' => ['003', 'Iván', 'Ejecutor Interno Demo', 1, 0, 'Mecánico'],
            // Ejecutor externo (proveedor).
            'ejecutor_externo' => ['004', 'Elena', 'Ejecutora Externa Demo', 1, 1, 'Taller externo'],
        ];

        $employees = [];

        foreach ($definitions as $key => [$seq, $firstName, $lastName, $executor, $external, $position]) {
            $employee = Employee::firstOrCreate(
                // identification_number es UNIQUE global: el prefijo por sucursal
                // evita choques entre demos y con los empleados reales.
                ['identification_number' => $prefix . '-' . $seq],
                [
                    'branch_id' => $branch->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone_number' => '0000000000',
                    'address' => 'Dirección ficticia',
                    'executor' => $executor,
                    'external' => $external,
                    'position' => $position,
                ]
            );

            // Al ser UNIQUE global, firstOrCreate podría devolver un empleado de
            // OTRA sucursal (por ejemplo si la sucursal demo se borró y se
            // recreó con otro ID). Usarlo mezclaría datos entre sucursales: se
            // aborta en vez de continuar.
            if ((int) $employee->branch_id !== (int) $branch->id) {
                throw new RuntimeException(
                    "Abortado: el empleado {$employee->identification_number} pertenece a la sucursal " .
                    "{$employee->branch_id} y no a la sucursal demo {$branch->id}. " .
                    'Revisar manualmente antes de reintentar: no se modificó nada.'
                );
            }

            $employees[$key] = $employee;
        }

        return $employees;
    }

    /**
     * @return array<Equipment>
     */
    private function provisionEquipment(Branch $branch, array $definition, $project, $equipmentTypes): array
    {
        $types = $equipmentTypes->pluck('name')->values();
        $equipment = [];

        for ($i = 1; $i <= 3; $i++) {
            $item = Equipment::firstOrCreate(
                // placa es el identificador visible del equipo en la app.
                ['branch_id' => $branch->id, 'placa' => $definition['plate_prefix'] . '00' . $i],
                [
                    'type' => $types->get($i - 1, 'Camión Demo'),
                    'internal_code' => $definition['plate_prefix'] . '-C' . $i,
                    'owner' => 'Propio',
                    'serial_niv' => $definition['prefix'] . '-NIV-00' . $i,
                    'vehicle_model' => 'Modelo Demo ' . ($i % 2 === 0 ? 'B' : 'A'),
                    'brand_name' => 'Marca Demo ' . ($i % 2 === 0 ? 'Dos' : 'Uno'),
                    'model_year' => (string) (2020 + $i),
                    'color' => 'Blanco',
                    'origin' => 'Nacional',
                    // racda guarda el ÍNDICE del select, no el texto: 0=Si, 1=No,
                    // 2=N/A (ver EquipmentController::createData).
                    'racda' => '2',
                    'active' => 1,
                ]
            );

            // El uuid lo pone EquipmentObserver en "creating"; no se asigna acá.
            // Vínculo con el proyecto vía pivote equipment_project (la tabla
            // equipment no tiene project_id).
            $item->projects()->syncWithoutDetaching([$project->id]);

            $equipment[] = $item;
        }

        return $equipment;
    }

    /**
     * @param  array<string, Employee>  $employees
     * @return array<int, array<string, mixed>>
     */
    private function provisionUsers(
        Branch $branch,
        array $definition,
        array $employees,
        ?string $password,
        bool $resetPasswords
    ): array {
        $platform = $definition['platform'];
        $accounts = [];

        foreach (self::USERS as $suffix => $roleName) {
            $email = $platform . '.' . $suffix . '@' . self::EMAIL_DOMAIN;

            $user = User::where('email', $email)->first();
            $isNew = $user === null;
            $plainPassword = null;

            // La verificación va ANTES de cualquier escritura: si la cuenta
            // existe y no es nuestra, se aborta sin haberla tocado.
            if (!$isNew) {
                $this->assertUserIsSafeToBind($user, $branch, $roleName);
            }

            if ($isNew || $resetPasswords) {
                $plainPassword = $password ?: (config('demo.default_password') ?: Str::password(16));
            }

            if ($isNew) {
                $user = User::create([
                    'name' => 'Demo ' . Str::ucfirst($suffix) . ' ' . Str::upper($platform),
                    'email' => $email,
                    'password' => Hash::make($plainPassword),
                    'phone' => '0000000000',
                    // Pre-verificado: el login de la API rechaza con
                    // EMAIL_NOT_VERIFIED y un revisor no puede abrir el correo.
                    'email_verified_at' => now(),
                ]);
            } elseif ($resetPasswords) {
                $user->password = Hash::make($plainPassword);
                $user->save();
            }

            // email_verified_at puede faltar si la cuenta fue creada antes por
            // otra vía; se repara sin tocar nada más.
            if (!$user->hasVerifiedEmail()) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            $role = Role::where('name', $roleName)->where('guard_name', 'sanctum')->firstOrFail();
            $user->roles()->sync([$role->id]);

            // sync() escribe en la tabla pivote sin pasar por Spatie, así que el
            // caché de permisos queda desactualizado en el mismo request (las
            // abilities del token se calculan con getAllPermissions()).
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            // Exactamente un user_branch: BranchHelper toma branches()->first().
            $user->userBranches()->firstOrCreate(['branch_id' => $branch->id]);

            // El rol Operador no puede iniciar sesión en la API sin un empleado
            // asociado (AuthController devuelve OPERATOR_WITHOUT_EMPLOYEE).
            if ($roleName === 'Operador') {
                $user->employees()->syncWithoutDetaching([$employees['operador']->id]);
            }

            $accounts[] = [
                'email' => $email,
                'role' => $roleName,
                'created' => $isNew,
                'password' => $plainPassword,
                'password_note' => $plainPassword === null
                    ? 'La cuenta ya existía: la contraseña no se modificó.'
                    : 'Guardar ahora: no se vuelve a mostrar.',
            ];
        }

        return $accounts;
    }

    /**
     * Impide que el provisioning se apropie de una cuenta que no sea suya. Más
     * abajo se hace roles()->sync() y se asigna la sucursal demo, así que una
     * cuenta real que casualmente tuviera uno de estos emails podría perder sus
     * roles. Se aborta en dos escenarios:
     *
     *   a) ya está vinculada a otra sucursal (típicamente la real);
     *   b) no tiene sucursal pero sí roles distintos al esperado (por ejemplo una
     *      cuenta Admin suelta).
     */
    private function assertUserIsSafeToBind(User $user, Branch $branch, string $expectedRole): void
    {
        $otherBranchIds = $user->userBranches()
            ->where('branch_id', '!=', $branch->id)
            ->pluck('branch_id')
            ->all();

        if (!empty($otherBranchIds)) {
            throw new RuntimeException(
                "Abortado: el usuario {$user->email} ya está vinculado a la(s) sucursal(es) " .
                implode(', ', $otherBranchIds) . ", distinta(s) de la sucursal demo {$branch->id}. " .
                'Revisar manualmente antes de reintentar: no se modificó nada.'
            );
        }

        $unexpectedRoles = $user->roles()
            ->where('name', '!=', $expectedRole)
            ->pluck('name')
            ->all();

        if (!empty($unexpectedRoles)) {
            throw new RuntimeException(
                "Abortado: el usuario {$user->email} tiene el/los rol(es) " .
                implode(', ', $unexpectedRoles) . " en vez de '{$expectedRole}'. " .
                'No parece una cuenta demo: revisar manualmente antes de reintentar.'
            );
        }
    }

    /**
     * Fallas de ejemplo para que el revisor no vea listados vacíos. Se crean con
     * Eloquent directo (no por el controlador), así no disparan correo ni push.
     */
    private function provisionSampleFaults(
        Branch $branch,
        array $definition,
        array $employees,
        array $equipment,
        array $catalogs
    ): int {
        $samples = [
            [
                'seq' => 'F1',
                'description' => 'Falla de ejemplo: ruido anormal en el motor. Registro ficticio de demostración.',
                'status' => $catalogs['fault_statuses']->firstWhere('name', 'En ejecución'),
                'reporter' => 'reportante',
            ],
            [
                'seq' => 'F2',
                'description' => 'Falla de ejemplo: fuga de aceite detectada en inspección. Registro ficticio de demostración.',
                'status' => $catalogs['fault_statuses']->firstWhere('name', 'En espera de repuesto'),
                'reporter' => 'reportante',
            ],
            [
                // IMPRESCINDIBLE: el rol Operador solo ve las fallas que él mismo
                // reportó — index y show filtran por reported_by_id contra su
                // empleado (ver FaultController::filteredQuery y ::show). Sin una
                // falla reportada por su empleado, el revisor que entre con la
                // cuenta *.operador@ vería el listado vacío.
                'seq' => 'F3',
                'description' => 'Falla de ejemplo reportada por el operador: vibración en el eje delantero. Registro ficticio de demostración.',
                'status' => $catalogs['fault_statuses']->firstWhere('name', FaultStatus::OPERATOR_STATUS_NAME),
                'reporter' => 'operador',
            ],
        ];

        $count = 0;

        foreach ($samples as $index => $sample) {
            $status = $sample['status'] ?: $catalogs['fault_statuses']->first();

            Fault::firstOrCreate(
                ['internal_id' => $definition['prefix'] . '-' . $sample['seq']],
                [
                    'branch_id' => $branch->id,
                    'employee_reported_id' => $employees[$sample['reporter']]->id,
                    'equipment_id' => $equipment[$index % count($equipment)]->id,
                    'service_area_id' => $catalogs['service_areas']->first()->id,
                    'description' => $sample['description'],
                    'fault_status_id' => $status->id,
                    'spare_part_status_id' => $catalogs['spare_part_statuses']->first()->id,
                    'report_date' => now()->subDays($index + 1)->toDateString(),
                ]
            );

            $count++;
        }

        return $count;
    }

    /**
     * Verificación posterior: confirma que los 7 catálogos que alimentan los
     * dropdowns de "crear falla" tienen datos para la sucursal. Se reporta en la
     * respuesta del endpoint para no depender de una revisión manual.
     *
     * @return array<string, int>
     */
    public function dropdownReport(Branch $branch): array
    {
        $branchId = $branch->id;

        return [
            'equipment' => FaultService::equipment($branchId)->count(),
            'service_area' => FaultService::serviceArea($branchId)->count(),
            'fault_status' => FaultService::faultStatus($branchId)->count(),
            'spare_part_status' => FaultService::sparePartStatuses($branchId)->count(),
            'employee_reported' => FaultService::employeeReported($branchId)->count(),
            // executors() ya incluye el placeholder "Seleccione": se descuenta
            // para reportar cuántas personas reales hay disponibles.
            'executors_internal' => max(0, FaultService::executors($branchId)->count() - 1),
            'executors_external' => max(0, FaultService::externalExecutors($branchId)->count() - 1),
            'projects' => FaultService::projects($branchId)->count(),
        ];
    }
}
