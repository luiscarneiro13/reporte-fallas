<?php

namespace App\Http\Controllers\V1\AdminBranch;

use App\Http\Controllers\Controller;
use App\Models\Fault;
use App\Services\FaultService;
use Illuminate\Http\Request;

class FaultController extends Controller
{
    public function index(Request $request) {}

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

    public function store(Request $request) {}

    public function update(Request $request, string $id) {}

    public function destroy(string $id) {}
}
