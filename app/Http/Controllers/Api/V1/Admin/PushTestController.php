<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExpoPushService;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Herramienta de diagnóstico (spec §6.5): permite verificar el pipeline
 * completo Expo -> dispositivo real sin depender de crear/cerrar una falla.
 * Autorización manual (no hay permiso Spatie dedicado): solo Super Admin/Admin.
 */
class PushTestController extends Controller
{
    use ApiResponse;

    public function __construct(private ExpoPushService $expo)
    {
    }

    public function send(Request $request)
    {
        if (!$request->user()->hasRole(['Super Admin', 'Admin'], 'sanctum')) {
            return $this->error('Forbidden', 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:500'],
            'type' => ['nullable', 'string', 'max:60'],
            'fault_id' => ['nullable', 'integer'],
            'equipment_uuid' => ['nullable', 'string', 'max:64'],
        ]);

        if ($validator->fails()) {
            return $this->error('The given data was invalid.', 422, $validator->errors());
        }

        $data = $validator->validated();
        $userId = $data['user_id'];
        unset($data['user_id']);

        $summary = $this->expo->sendToUser($userId, $data);

        return $this->success($summary, 'Push test dispatched');
    }
}
