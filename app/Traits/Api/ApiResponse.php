<?php

namespace App\Traits\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Envelope JSON único para la API /api/v1 (ver docs/api-endpoints-spec.md §3-4):
 * {success, message, data} en éxito y error, incluyendo los 422 de validación
 * (ver App\Http\Requests\Api\V1\ApiFormRequest).
 */
trait ApiResponse
{
    protected function success($data = null, string $message = 'Operación exitosa', int $code = 200): JsonResponse
    {
        $payload = ['success' => true, 'message' => $message];

        if ($data !== null) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $code);
    }

    protected function created($data = null, string $message = 'Creado exitosamente'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function error(string $message, int $code = 400, $errors = null, ?string $errorCode = null): JsonResponse
    {
        $payload = ['success' => false, 'message' => $message];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        if ($errorCode !== null) {
            $payload['error_code'] = $errorCode;
        }

        return response()->json($payload, $code);
    }

    protected function paginatedResponse(LengthAwarePaginator $paginator, string $message = 'Operación exitosa'): JsonResponse
    {
        return $this->success([
            'data' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ], $message);
    }
}
