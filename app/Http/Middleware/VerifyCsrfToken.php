<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;

class VerifyCsrfToken
{
    protected $except = [
        'api/*',
    ];

    public function handle(Request $request, $next)
    {
        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        $token = $_POST['_token'] ?? $request->header('X-CSRF-TOKEN');
        $sessionToken = session('_token');

        if (!$token || $token !== $sessionToken) {
            if ($request->isJson()) {
                return Response::json(['error' => 'CSRF token mismatch'], 403);
            }
            abort(403, 'CSRF token mismatch');
        }

        return $next($request);
    }

    protected function inExceptArray(Request $request)
    {
        foreach ($this->except as $except) {
            if (fnmatch($except, $request->path())) {
                return true;
            }
        }
        return false;
    }
}



