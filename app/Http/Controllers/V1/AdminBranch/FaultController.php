<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\FaultRequest;
use App\Models\Fault;
use App\Services\FaultService;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class FaultController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.faults.index";

    public function index(Request $request)
    {
        // 1. Capturar el ID de la sucursal de la sesión
        $branchId = session('branch')->id;

        // 2. Capturar el parámetro de búsqueda
        $query = $request->query('query');

        // 3. Iniciar la consulta con Carga Ansiosa (Eager Loading)
        $equipmentQuery = Fault::query()
            // FILTRAR POR LA SUCURSAL DEL USUARIO EN SESIÓN
            ->where('branch_id', $branchId);

        // 4. Aplicar filtro de búsqueda si existe un query
        if ($query) {
            $equipmentQuery->where(function ($q) use ($query) {

                // Criterios directos (placa, código interno)
                $q->where('id', ltrim($query, '0'))
                    ->orWhere('description', 'like', "%{$query}%")
                ;

            });
        }

        $equipmentQuery->orderBy('faults.id', 'desc');
        // 5. Ejecutar la consulta con paginación
        $faults = $equipmentQuery->paginate(10);

        // 6. Devolver la vista con los resultados
        return view('V1.AdminBranch.Faults.index', compact('faults'));
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        $employeeReported = FaultService::employeeReported();
        $equipment = FaultService::equipment();
        $serviceArea = FaultService::serviceArea();
        $faultStatus = FaultService::faultStatus();
        $sparePartStatuses = FaultService::sparePartStatuses();
        $executors = FaultService::executors();
        return view(
            'V1.AdminBranch.Faults.create',
            compact('back_url')
                + compact('employeeReported')
                + compact('equipment')
                + compact('serviceArea')
                + compact('faultStatus')
                + compact('sparePartStatuses')
                + compact('executors')
        );
    }
    public function edit(string $id) {}

    public function store(FaultRequest $request)
    {
        return $this->saveOrUpdate($request);
    }

    public function update(FaultRequest $request, string $id)
    {
        // El tipo de $request en el método update es FaultRequest
        return $this->saveOrUpdate($request, $id);
    }

    /**
     * Guarda o actualiza una falla usando los datos validados del FaultRequest.
     *
     * @param FaultRequest $request Los datos ya validados.
     * @param string|null $id El ID de la falla a actualizar.
     */
    private function saveOrUpdate(FaultRequest $request, string $id = null) // ⬅️ CAMBIO 1: El tipo de Request es FaultRequest
    {
        try {
            // 1. Obtener o crear el modelo
            $item = $id ? Fault::find($id) : new Fault();
            if (!$item) {
                return $this->alertError(self::INDEX, 'Falla no encontrada para actualizar.');
            }

            // 2. Asignación Masiva Segura (Mass Assignment)
            // Esto reemplaza todas las asignaciones campo por campo.
            $item->fill($request->validated()); // ⬅️ CAMBIO 2: Uso de fill con datos validados

            // 3. Atributo de sucursal (se mantiene la asignación manual)
            // Se asigna al final porque no es parte del input del usuario.
            $item->branch_id = session('branch')->id;

            $item->save();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'data' => $item]);
            }

            if (request()->back_url) {
                return redirect(request()->back_url);
            }

            $action_msg = $id ? 'actualizada' : 'creada';
            $message = "Falla {$action_msg} ";
            return $this->alertSuccess(self::INDEX, $message);
        } catch (\Throwable $th) {
            // Manejo de errores
            $action = $id ? 'actualizar' : 'crear';
            info($th->getMessage());
            return $this->alertError(self::INDEX, "Error al {$action} la falla: " . $th->getMessage());
        }
    }

    public function destroy(string $id) {}
}
