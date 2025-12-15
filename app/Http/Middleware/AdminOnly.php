<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Request;
use App\Models\User;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        $userId = session('user_id');
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $user = User::find($userId);
        if (!$user || $user->role !== 'admin') {
            http_response_code(403);
            echo 'Access denied';
            exit;
        }

        return $next($request);
    }
}



