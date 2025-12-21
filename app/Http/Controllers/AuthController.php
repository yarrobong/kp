<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Request;
use App\Http\Response;
use App\Support\Hash;
use App\Support\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        $csrfToken = session('_token') ?: (session('_token', bin2hex(random_bytes(32))) ?: session('_token'));
        $error = session('error');
        $success = session('success');

        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Вход - КП Генератор</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <div class="auth-container">
                <div class="auth-card">
                    <h1>Вход в систему</h1>';

        if ($error) {
            echo '<div class="alert alert-error">' . htmlspecialchars($error) . '</div>';
        }
        if ($success) {
            echo '<div class="alert alert-success">' . htmlspecialchars($success) . '</div>';
        }

        echo '
                    <form method="POST" action="/login">
                        <input type="hidden" name="_token" value="' . htmlspecialchars($csrfToken) . '">

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" required autofocus>
                        </div>

                        <div class="form-group">
                            <label>Пароль</label>
                            <input type="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Войти</button>
                    </form>

                    <div class="text-center" style="margin-top: 20px;">
                        <p>Нет аккаунта? <a href="/register">Зарегистрироваться</a></p>
                    </div>

                    <div style="margin-top: 30px; padding: 20px; background: rgba(0,0,0,0.05); border-radius: 10px;">
                        <h3 style="text-align: center; margin-bottom: 15px;">Тестовые аккаунты</h3>

                        <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <div style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer;" onclick="fillForm(\'admin@example.com\', \'password\')">
                                <div style="font-size: 24px; text-align: center;">👑</div>
                                <div style="text-align: center; font-weight: bold;">Администратор</div>
                                <div style="text-align: center; font-size: 12px;">admin@example.com</div>
                            </div>
                            <div style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer;" onclick="fillForm(\'user@example.com\', \'password\')">
                                <div style="font-size: 24px; text-align: center;">👤</div>
                                <div style="text-align: center; font-weight: bold;">Пользователь</div>
                                <div style="text-align: center; font-size: 12px;">user@example.com</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            function fillForm(email, password) {
                document.querySelector(\'input[name="email"]\').value = email;
                document.querySelector(\'input[name="password"]\').value = password;
                setTimeout(() => {
                    document.querySelector(\'form\').submit();
                }, 500);
            }
            </script>
        </body>
        </html>';
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::query()->where('email', '=', $email)->first();

        if (!$user || !\App\Support\Hash::check($password, $user->password)) {
            if ($request->expectsJson()) {
                return \App\Http\Response::json(['error' => 'Invalid credentials'], 401);
            }
            session('error', 'Неверный email или пароль');
            return \App\Http\Redirect::to('/login');
        }

        session('user_id', $user->id);
        session('user_name', $user->name);
        session('user_email', $user->email);
        session('user_role', $user->role);

        if ($request->expectsJson()) {
            return \App\Http\Response::json(['message' => 'Success', 'user' => $user]);
        }

        return redirect('/proposals');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return Response::json(['errors' => $validator->errors()], 422);
            }
            return redirect('/register')->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'role' => 'user',
        ]);

        session(['user_id' => $user->id, 'user_name' => $user->name, 'user_role' => $user->role]);

        if ($request->expectsJson()) {
            return Response::json(['message' => 'Registered successfully', 'user' => $user], 201);
        }

        return redirect('/proposals');
    }

    public function logout(Request $request)
    {
        session()->forget(['user_id', 'user_name', 'user_role']);

        if ($request->expectsJson()) {
            return Response::json(['message' => 'Logged out']);
        }

        return redirect('/login');
    }
}

