<?php

namespace App\Http\Middleware;

use Closure;

class VerifyCsrfToken
{
    protected $except = [
        'api/*',
    ];

    public function handle($request, Closure $next)
    {
        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        $token = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');
        $sessionToken = session('_token');

        if (!$token || $token !== $sessionToken) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'CSRF token mismatch'], 403);
            }
            abort(403, 'CSRF token mismatch');
        }

        return $next($request);
    }

    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($request->is($except)) {
                return true;
            }
        }
        return false;
    }
}



