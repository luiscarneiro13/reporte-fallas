<?php

namespace App\Http\Controllers\Api\V1\AdminBranch;

use App\Helpers\BranchHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdminBranch\FaultRequest;
use App\Models\Fault;
use App\Models\FaultHistory;
use App\Models\FaultStatus;
use App\Models\FaultView;
use App\Mail\CerrarFallaEmail;
use App\Mail\ReportarFallaEmail;
use App\Services\FaultService;
use App\Services\PushNotificationService;
use App\Traits\Api\ApiResponse;
use App\Traits\DateTransformerTrait;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Spec §6.2.7. Índice/show leen de la vista v_faults_base (modelo FaultView);
 * store/update escriben en la tabla base `faults`. Al cerrar una falla se
 * copia a `fault_history` y se borra de `faults` (archivado manual). A
 * diferencia del controlador web original, aquí las transacciones DB están
 * activas (el original las tenía comentadas "solo para fines de prueba" —
 * corregido según checklist §7).
 */
class FaultController extends Controller
{
    use ApiResponse;
    use DateTransformerTrait;
    use Sortable;

    const SORTABLE_COLUMNS = [
        'id', 'internal_code', 'equipment_name', 'description', 'fault_status_name',
        'spare_part_status_name', 'service_area_name', 'duration_days', 'report_date',
        'reported_by_name', 'executor_name',
    ];

    public function __construct(private PushNotificationService $pushService)
    {
        $base = 'Fallas';
        $this->middleware("permission:{$base} Crear")->only(['store']);
        $this->middleware("permission:{$base} Editar")->only(['update']);
        $this->middleware("permission:{$base} Eliminar")->only(['destroy']);
        $this->middleware("permission:{$base} Ver")->only(['index', 'show']);
    }

    public function createData(Request $request)
    {
        $branchId = BranchHelper::getBranchId();

        if (!$branchId) {
            return $this->error('No hay una sucursal asociada al usuario autenticado.', 400);
        }

        $user = $request->user();
        $isOperator = $user->hasRole('Operador', 'sanctum');

        // Mismos métodos de FaultService que usa la web (FaultController::create()),
        // pasando branch_id explícito porque la API es stateless (sin session('branch')).
        // Así el orden y el texto de cada catálogo quedan literalmente en un solo
        // lugar — no se pueden volver a desincronizar entre web y API.
        $defaultFaultStatusId = null;
        $defaultEmployeeReportedId = null;

        if ($isOperator) {
            $branch = \App\Models\Branch::find($branchId);
            $internalStatus = $branch->faultStatus()->firstOrCreate(['name' => FaultStatus::OPERATOR_STATUS_NAME]);
            $faultStatus = collect([$internalStatus->id => $internalStatus->name]);
            $defaultFaultStatusId = $internalStatus->id;
            $defaultEmployeeReportedId = $user->employees()->first()?->id;
        } else {
            // Solo lleva "Seleccione" cuando NO es Operador — para ese rol el
            // estatus queda fijo, igual que en FaultController::create() (web).
            $faultStatus = FaultService::faultStatus($branchId)->prepend('Seleccione', '0');
        }

        return $this->success([
            'equipment' => $this->toOptions(FaultService::equipment($branchId)->prepend('Seleccione', '0')),
            'service_area' => $this->toOptions(FaultService::serviceArea($branchId)->prepend('Seleccione', '0')),
            'fault_status' => $this->toOptions($faultStatus),
            'spare_part_status' => $this->toOptions(FaultService::sparePartStatuses($branchId)->prepend('Seleccione', '0')),
            'employee_reported' => $this->toOptions(FaultService::employeeReported($branchId)->prepend('Seleccione', '0')),
            'executors_internal' => $this->toOptions(FaultService::executors($branchId)),
            'executors_external' => $this->toOptions(FaultService::externalExecutors($branchId)),
            'default_fault_status_id' => $defaultFaultStatusId,
            'default_employee_reported_id' => $defaultEmployeeReportedId,
        ]);
    }

    /**
     * Catálogos para la barra de filtros del listado (V1\AdminBranch\FaultController::index,
     * web). Distinto de createData(): acá el placeholder es "Todos" (no
     * "Seleccione") y SÍ incluye proyectos — filtros del listado, no campos
     * de un formulario.
     */
    public function filtrosData()
    {
        $branchId = BranchHelper::getBranchId();

        if (!$branchId) {
            return $this->error('No hay una sucursal asociada al usuario autenticado.', 400);
        }

        return $this->success([
            'equipment' => $this->toOptions(FaultService::equipment($branchId)->prepend('Todos', '0')),
            'service_area' => $this->toOptions(FaultService::serviceArea($branchId)->prepend('Todos', '0')),
            'fault_status' => $this->toOptions(FaultService::faultStatus($branchId)->prepend('Todos', '0')),
            'spare_part_status' => $this->toOptions(FaultService::sparePartStatuses($branchId)->prepend('Todos', '0')),
            'projects' => $this->toOptions(FaultService::projects($branchId)->prepend('Todos', '0')),
        ]);
    }

    public function index(Request $request)
    {
        $result = $this->filteredQuery($request);
        $stats = $this->statsFor($result['base_query_clone']);

        $faults = $result['query']->paginate(10);

        return $this->success([
            'data' => $faults->items(),
            'pagination' => [
                'current_page' => $faults->currentPage(),
                'last_page' => $faults->lastPage(),
                'per_page' => $faults->perPage(),
                'total' => $faults->total(),
            ],
            'stats' => $stats,
        ]);
    }

    public function show(string $id)
    {
        $user = request()->user();
        $query = FaultView::query()->where('id', $id)->where('branch_id', BranchHelper::getBranchId());

        if ($user->hasRole('Operador', 'sanctum')) {
            $query->where('reported_by_id', $user->employees()->first()?->id);
        }

        $fault = $query->first();

        if (!$fault) {
            return $this->error('Falla no encontrada.', 404);
        }

        return $this->success($fault);
    }

    private function filteredQuery(Request $request): array
    {
        $branchId = BranchHelper::getBranchId();
        $user = $request->user();

        $baseQuery = FaultView::query()->where('branch_id', $branchId);

        if ($user->hasRole('Operador', 'sanctum')) {
            $baseQuery->where('reported_by_id', $user->employees()->first()?->id);
        }

        $applyFilters = function ($query) use ($request) {
            if ($name = $request->query('name')) {
                $query->where('reported_by_name', 'like', "%{$name}%");
            }
            if ($search = $request->query('query')) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', ltrim($search, '0') ?: '0')
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('internal_id', 'like', "%{$search}%")
                        ->orWhere('internal_code', 'like', "%{$search}%")
                        ->orWhere('equipment_name', 'like', "%{$search}%");
                });
            }
            if ($equipmentName = $request->query('equipment_name')) {
                $query->where('equipment_name', 'like', "%{$equipmentName}%");
            }
            if ($serviceAreaName = $request->query('service_area_name')) {
                $query->where('service_area_name', 'like', "%{$serviceAreaName}%");
            }
            foreach (['equipment_id', 'service_area_id', 'fault_status_id', 'spare_part_status_id', 'project_id', 'executor_id'] as $field) {
                if (($value = $request->query($field)) && $value != '0') {
                    $query->where($field, $value);
                }
            }
            if ($from = $request->query('from')) {
                $query->whereDate('report_date', '>=', $from);
            }
            if ($to = $request->query('to')) {
                $query->whereDate('report_date', '<=', $to);
            }
        };

        $query = (clone $baseQuery);
        $applyFilters($query);

        $closeStatus = $request->query('close_status');
        if ($closeStatus === '1') {
            $query->whereNotNull('closed_at');
        } elseif ($closeStatus === '2' || !$closeStatus) {
            $query->whereNull('closed_at');
        }

        // Mismo default que la web (V1\AdminBranch\FaultController::getFilteredFaultsQuery):
        // sin sort_by explícito, ordena por duration_days desc (las que llevan más tiempo primero).
        $this->applySort($query, $request, self::SORTABLE_COLUMNS, 'duration_days', 'desc');

        $statsBase = (clone $baseQuery);
        $applyFilters($statsBase);

        return ['query' => $query, 'base_query_clone' => $statsBase];
    }

    /**
     * Heurística de conteo por texto de fault_status_name (multi-idioma).
     * Spec §6.2.7 recomienda reemplazar esto por un campo de categoría real
     * en vez de matching de texto — se deja documentado como deuda técnica.
     */
    private function statsFor($query): array
    {
        $rows = (clone $query)->get(['id', 'closed_at', 'fault_status_name']);

        $open = $rows->whereNull('closed_at');

        return [
            'total' => $rows->count(),
            'open' => $open->count(),
            'closed' => $rows->whereNotNull('closed_at')->count(),
            'in_progress' => $open->filter(fn ($r) => str_contains(mb_strtolower((string) $r->fault_status_name), 'progreso'))->count(),
            'blocked' => $open->filter(fn ($r) => str_contains(mb_strtolower((string) $r->fault_status_name), 'bloque'))->count(),
            'scheduled' => $open->filter(fn ($r) => str_contains(mb_strtolower((string) $r->fault_status_name), 'program'))->count(),
        ];
    }

    public function store(FaultRequest $request)
    {
        return $this->saveOrUpdate($request);
    }

    public function update(FaultRequest $request, string $id)
    {
        return $this->saveOrUpdate($request, $id);
    }

    private function saveOrUpdate(FaultRequest $request, ?string $id = null)
    {
        $branchId = BranchHelper::getBranchId();
        $validatedData = $request->validated();
        $isClosing = isset($validatedData['closed']);

        $item = $id ? Fault::where('id', $id)->where('branch_id', $branchId)->first() : new Fault();

        if ($id && !$item) {
            return $this->error('Falla no encontrada.', 404);
        }

        DB::beginTransaction();

        try {
            $dateFields = ['report_date', 'scheduled_execution', 'completed_execution'];
            foreach ($dateFields as $field) {
                if (!empty($validatedData[$field]) && !$this->looksLikeMysqlDate($validatedData[$field])) {
                    $validatedData[$field] = $this->transformDateFields([$field => $validatedData[$field]], [$field])[$field];
                }
            }

            $item->fill($validatedData);
            $item->branch_id = $branchId;
            $item->save();

            if ($isClosing) {
                $historyRecord = FaultView::find($item->id);

                if (!$historyRecord) {
                    DB::rollBack();
                    return $this->error('Error al obtener datos de la vista para archivar.', 500);
                }

                $historyData = $historyRecord->getAttributes();
                $historyData['original_fault_id'] = $item->id;

                if (isset($historyData['closed']) && !isset($historyData['closed_at'])) {
                    $historyData['closed_at'] = $historyData['closed'];
                }

                unset($historyData['id'], $historyData['closed'], $historyData['duration_days']);

                FaultHistory::create($historyData);
                $item->delete();

                DB::commit();

                try {
                    Mail::to(env('EMAIL_FALLAS'))->send(new CerrarFallaEmail($historyRecord));
                } catch (\Throwable $th) {
                    report($th);
                }

                $this->pushService->notifyClosedFault($item);

                return $this->success(null, 'Falla cerrada y archivada correctamente.');
            }

            DB::commit();

            $faultView = FaultView::find($item->id);

            try {
                Mail::to(env('EMAIL_FALLAS'))->send(new ReportarFallaEmail($faultView));
            } catch (\Throwable $th) {
                report($th);
            }

            if (!$id) {
                $this->pushService->notifyNewFault($item);
            }

            return $this->success($faultView, $id ? 'Falla actualizada' : 'Falla creada', $id ? 200 : 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            $action = $id ? 'actualizar' : 'crear';
            report($th);
            return $this->error("Error al {$action} la falla: " . $th->getMessage(), 500);
        }
    }

    private function looksLikeMysqlDate(string $value): bool
    {
        return (bool) preg_match('/^\d{4}-\d{2}-\d{2}/', $value);
    }

    public function destroy(string $id)
    {
        $item = Fault::where('id', $id)->where('branch_id', BranchHelper::getBranchId())->first();

        if (!$item) {
            return $this->error('Falla no encontrada.', 404);
        }

        $item->delete();

        return $this->success(null, 'Falla eliminada exitosamente.');
    }
}
