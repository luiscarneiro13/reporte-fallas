<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Cache;

/**
 * Bloqueo de cuenta por intentos fallidos de login (spec §6.1, error_code
 * ACCOUNT_LOCKED). Implementado sobre cache (sin tabla nueva): 5 intentos
 * fallidos por cuenta bloquean el login durante 15 minutos.
 */
class LoginLockoutService
{
    protected int $maxAttempts = 5;
    protected int $lockMinutes = 15;

    public function isLocked(string $email): bool
    {
        return Cache::has($this->lockKey($email));
    }

    public function registerFailedAttempt(string $email): void
    {
        $key = $this->attemptsKey($email);
        $attempts = (int) Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, now()->addMinutes($this->lockMinutes));

        if ($attempts >= $this->maxAttempts) {
            Cache::put($this->lockKey($email), true, now()->addMinutes($this->lockMinutes));
        }
    }

    public function clear(string $email): void
    {
        Cache::forget($this->attemptsKey($email));
        Cache::forget($this->lockKey($email));
    }

    protected function attemptsKey(string $email): string
    {
        return 'login_attempts:' . strtolower($email);
    }

    protected function lockKey(string $email): string
    {
        return 'login_locked:' . strtolower($email);
    }
}
