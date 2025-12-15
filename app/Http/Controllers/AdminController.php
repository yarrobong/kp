<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Proposal;
use App\Models\Template;
use App\Http\Request;
use App\Http\Response;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'users' => User::count(),
            'proposals' => Proposal::count(),
            'templates' => Template::count(),
            'published_proposals' => Proposal::query()->where('status', '=', 'published')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function users(Request $request)
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);

        if ($request->expectsJson()) {
            return Response::json($users);
        }

        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $role = $request->input('role');

        if (!in_array($role, ['guest', 'user', 'admin'])) {
            return Response::json(['error' => 'Invalid role'], 400);
        }

        $user->update(['role' => $role]);

        if ($request->expectsJson()) {
            return Response::json($user);
        }

        session('success', 'Роль обновлена');
        redirect_back();
    }
}

