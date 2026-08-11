<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException as PermissionUnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Unifica el envelope {success,message} también para excepciones que no
     * pasan por un controlador/FormRequest (401 sin sesión, 403 de permisos,
     * 404 de ruta, 500 no controlado), solo para /api/v1/* — spec §3-4,
     * recomendación de unificar convenciones en toda la API.
     */
    public function render($request, Throwable $e)
    {
        if (!str_starts_with($request->path(), 'api/v1')) {
            return parent::render($request, $e);
        }

        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
        }

        if ($e instanceof PermissionUnauthorizedException) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para realizar esta acción.'], 403);
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return response()->json(['success' => false, 'message' => 'Recurso no encontrado.'], 404);
        }

        if ($e instanceof HttpExceptionInterface) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Error al procesar la solicitud.',
            ], $e->getStatusCode());
        }

        return response()->json([
            'success' => false,
            'message' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor.',
        ], 500);
    }
}
