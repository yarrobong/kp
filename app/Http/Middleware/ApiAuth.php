<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class ApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?? $request->header('X-API-Token');
        
        if (!$token) {
            return response()->json(['error' => 'Token required'], 401);
        }

        // Простая проверка токена (в реальности используйте Laravel Sanctum)
        $user = User::where('api_token', $token)->first();
        
        if (!$user) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}



