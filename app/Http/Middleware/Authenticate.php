<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Redirect;
use App\Http\Response;

class Authenticate
{
    public function handle(Request $request, $next)
    {
        if (!session('user_id')) {
            if ($request->isJson()) {
                return Response::json(['error' => 'Unauthorized'], 401);
            }
            return Redirect::to('/login');
        }
        return $next($request);
    }
}



