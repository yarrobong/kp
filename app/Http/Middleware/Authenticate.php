<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Request;
use App\Http\Response;

class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('user_id')) {
            if ($request->expectsJson()) {
                return Response::json(['error' => 'Unauthorized'], 401);
            }
            header('Location: /login');
            exit;
        }
        return $next($request);
    }
}



