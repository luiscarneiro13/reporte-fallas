<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Models\User;
use App\Services\Api\LoginLockoutService;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Autenticación API (spec §6.1). No incluye locale/PUT user/language ni
 * expo_token: este proyecto no tiene módulo de idiomas (tabla `languages`)
 * ni push notifications todavía — quedan fuera de la Fase 1 (ver checklist
 * al final de docs/api-endpoints-spec.md).
 */
class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(protected LoginLockoutService $lockout)
    {
    }

    public function login(LoginRequest $request)
    {
        $email = $request->input('email');

        if ($this->lockout->isLocked($email)) {
            return $this->error(
                'Cuenta bloqueada temporalmente por demasiados intentos fallidos. Intenta de nuevo más tarde.',
                429,
                null,
                'ACCOUNT_LOCKED'
            );
        }

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            if ($user) {
                $this->lockout->registerFailedAttempt($email);
            }

            return $this->error('Las credenciales proporcionadas son incorrectas.', 401);
        }

        $this->lockout->clear($email);

        if (!$user->hasVerifiedEmail()) {
            return $this->error(
                'Debes verificar tu correo electrónico antes de iniciar sesión.',
                403,
                null,
                'EMAIL_NOT_VERIFIED'
            );
        }

        if ($user->hasRole('Operador', 'sanctum') && !$user->employees()->exists()) {
            return $this->error(
                'El usuario Operador no tiene un empleado asignado.',
                403,
                null,
                'OPERATOR_WITHOUT_EMPLOYEE'
            );
        }

        $abilities = $user->getAllPermissions()->pluck('name')->toArray();
        $tokenName = 'api-auth-' . now()->format('YmdHis') . '-' . Str::random(8);
        $token = $user->createToken($tokenName, $abilities)->plainTextToken;

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'cedula' => $user->cedula,
                'roles' => $user->getRoleNames(),
                'permissions' => $abilities,
                'employee_id' => $user->employees()->first()?->id,
            ],
            'token' => $token,
            'token_name' => $tokenName,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Sesión cerrada exitosamente.');
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->success(null, 'Sesión cerrada en todos los dispositivos.');
    }
}
