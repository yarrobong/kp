<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Models\User;

class ApiAuth
{
    public function handle(Request $request, $next)
    {
        $token = $request->header('Authorization') ?? $request->header('X-API-Token');

        if (!$token) {
            return Response::json(['error' => 'Token required'], 401);
        }

        // Убираем "Bearer " если есть
        $token = str_replace('Bearer ', '', $token);

        // Простая проверка токена
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return Response::json(['error' => 'Invalid token'], 401);
        }

        // Сохраняем пользователя в сессии для API запросов
        session('api_user', $user);

        return $next($request);
    }
}



