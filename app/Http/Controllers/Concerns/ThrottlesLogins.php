<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

trait ThrottlesLogins
{
    protected int $maxLoginAttempts = 5;

    protected function loginThrottleKey(Request $request, string $scope): string
    {
        return $scope.'|'.Str::lower((string) $request->input('email')).'|'.$request->ip();
    }

    protected function loginLockMessage(string $key): ?string
    {
        if (! RateLimiter::tooManyAttempts($key, $this->maxLoginAttempts)) {
            return null;
        }

        return 'Terlalu banyak percobaan login. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.';
    }
}