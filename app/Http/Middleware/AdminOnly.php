<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Redirect;
use App\Models\User;

class AdminOnly
{
    public function handle(Request $request, $next)
    {
        $userId = session('user_id');
        if (!$userId) {
            return Redirect::to('/login');
        }

        $user = User::find($userId);
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Access denied');
        }

        return $next($request);
    }
}



