<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect('/login');
        }

        $user = User::find($userId);
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Access denied');
        }

        return $next($request);
    }
}



