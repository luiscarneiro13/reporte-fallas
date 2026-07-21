<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\EmployeeEmploymentPeriodRequest;
use App\Models\Cargo;
use App\Models\ContractType;
use App\Models\Employee;
use App\Models\EmployeeEmploymentPeriod;
use App\Traits\DateTransformerTrait;
use Illuminate\Http\Request;

class EmployeeEmploymentPeriodController extends Controller
{
    use DateTransformerTrait;

    public function __construct()
    {
        $basePermission = "Periodos de Empleados";
        $this->middleware('permission:' . $basePermission . ' Crear')->only(['create', 'store']);
        $this->middleware('permission:' . $basePermission . ' Editar')->only(['edit', 'update']);
        $this->middleware('permission:' . $basePermission . ' Eliminar')->only('destroy');
    }

    public function create()
    {
        $back_url = request()->back_url ?? null;
        $employee = Employee::findOrFail(request()->employee_id);
        [$contractTypes, $cargos] = $this->selectsForBranch();

        return view('V1.AdminBranch.EmployeeEmploymentPeriods.create', compact('back_url', 'employee', 'contractTypes', 'cargos'));
    }

    public function edit(EmployeeEmploymentPeriod $periodos_empleado)
    {
        $back_url = request()->back_url ?? null;
        $employee = $periodos_empleado->employee;
        [$contractTypes, $cargos] = $this->selectsForBranch();

        return view('V1.AdminBranch.EmployeeEmploymentPeriods.edit', [
            'back_url' => $back_url,
            'employee' => $employee,
            'period' => $periodos_empleado,
            'contractTypes' => $contractTypes,
            'cargos' => $cargos,
        ]);
    }

    public function store(EmployeeEmploymentPeriodRequest $request)
    {
        return $this->saveOrUpdate($request);
    }

    public function update(EmployeeEmploymentPeriodRequest $request, EmployeeEmploymentPeriod $periodos_empleado)
    {
        return $this->saveOrUpdate($request, $periodos_empleado);
    }

    private function saveOrUpdate(Request $request, EmployeeEmploymentPeriod $period = null)
    {
        try {
            $validatedData = $request->validated();
            $validatedData = $this->transformDateFields($validatedData, ['start_date', 'end_date']);

            $item = $period ?? new EmployeeEmploymentPeriod();
            $item->employee_id = $validatedData['employee_id'];
            $item->branch_id = session('branch')->id;
            $item->contract_type_id = $request->input('contract_type_id') ?: null;
            $item->cargo_id = $request->input('cargo_id') ?: null;
            $item->start_date = $validatedData['start_date'];
            $item->end_date = $validatedData['end_date'];
            $item->termination_reason = $request->input('termination_reason');
            $item->notes = $request->input('notes');
            $item->save();

            $action_msg = $period ? 'actualizado' : 'creado';
            return $this->redirectBack($item->employee_id, 'success', "Período de empleo {$action_msg}.");
        } catch (\Throwable $th) {
            info($th->getMessage());
            $action = $period ? 'actualizar' : 'crear';
            $employeeId = $period->employee_id ?? $request->input('employee_id');
            return $this->redirectBack($employeeId, 'error', "Error al {$action} el período: " . $th->getMessage());
        }
    }

    public function destroy(EmployeeEmploymentPeriod $periodos_empleado)
    {
        try {
            $employeeId = $periodos_empleado->employee_id;
            $periodos_empleado->delete();
            return $this->redirectBack($employeeId, 'success', 'Período de empleo eliminado.');
        } catch (\Throwable $th) {
            return $this->redirectBack($periodos_empleado->employee_id, 'error', 'Error al eliminar el período: ' . $th->getMessage());
        }
    }

    private function redirectBack($employeeId, string $state, string $message)
    {
        $url = request()->back_url ?: route('admin.sucursal.employees.edit', $employeeId);
        return redirect($url)->with(['state' => $state, 'message' => $message]);
    }

    private function selectsForBranch(): array
    {
        $contractTypes = ContractType::where('branch_id', session('branch')->id)->pluck('name', 'id')->prepend('Sin tipo de contrato', '0');
        $cargos = Cargo::where('branch_id', session('branch')->id)->pluck('name', 'id')->prepend('Sin cargo', '0');

        return [$contractTypes, $cargos];
    }
}
