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
        $this->view('auth.login');
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::query()->where('email', '=', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            if ($request->expectsJson()) {
                return Response::json(['error' => 'Invalid credentials'], 401);
            }
            session('error', 'Неверный email или пароль');
            $this->redirect('/login');
            return;
        }

        session(['user_id' => $user->id, 'user_name' => $user->name, 'user_role' => $user->role]);

        if ($request->expectsJson()) {
            return Response::json(['message' => 'Success', 'user' => $user]);
        }

        $this->redirect('/proposals');
    }

    public function showRegister()
    {
        $this->view('auth.register');
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
            session('errors', $validator->errors());
            $this->redirect('/register');
            return;
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

        $this->redirect('/proposals');
    }

    public function logout(Request $request)
    {
        if (session('user_id')) {
            unset($_SESSION['user_id']);
        }
        if (session('user_name')) {
            unset($_SESSION['user_name']);
        }
        if (session('user_role')) {
            unset($_SESSION['user_role']);
        }

        if ($request->expectsJson()) {
            return Response::json(['message' => 'Logged out']);
        }

        $this->redirect('/login');
    }
}

