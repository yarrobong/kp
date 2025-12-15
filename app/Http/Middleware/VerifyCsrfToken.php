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
                return \App\Http\Response::json(['error' => 'CSRF token mismatch'], 403);
            }
            http_response_code(403);
            echo 'CSRF token mismatch';
            exit;
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



