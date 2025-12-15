<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Http\Request;
use App\Http\Response;
use App\Support\Validator;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = Template::with('user');

        $userId = session('user_id');
        $userRole = session('user_role');

        if ($userRole !== 'admin') {
            $query->where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhere(function($q2) {
                      $q2->where('is_system', true)->where('is_published', true);
                  });
            });
        }

        $templates = $query->orderBy('created_at', 'desc')->get();

        if ($request->expectsJson()) {
            return Response::json($templates);
        }

        return view('templates.index', compact('templates'));
    }

    public function create()
    {
        return view('templates.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'body_html' => 'required|string',
            'variables' => 'nullable|array',
            'is_system' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return Response::json(['errors' => $validator->errors()], 422);
            }
            session('errors', $validator->errors());
            session('_old_input', $request->input());
            redirect_back();
            return;
        }

        $data = $request->input();
        $userRole = session('user_role');

        // Только админ может создавать системные шаблоны
        if ($userRole !== 'admin') {
            $data['is_system'] = false;
            $data['user_id'] = session('user_id');
        } else {
            $data['user_id'] = $data['user_id'] ?? session('user_id');
        }

        $template = Template::create($data);

        if ($request->expectsJson()) {
            return Response::json($template, 201);
        }

        session('success', 'Шаблон создан');
        redirect('/templates');
    }

    public function show(Request $request, $id)
    {
        $template = Template::findOrFail($id);

        $userId = session('user_id');
        $userRole = session('user_role');

        if ($userRole !== 'admin' && $template->user_id !== $userId && (!$template->is_system || !$template->is_published)) {
            abort(403);
        }

        if ($request->expectsJson()) {
            return Response::json($template);
        }

        return view('templates.show', compact('template'));
    }

    public function edit($id)
    {
        $template = Template::findOrFail($id);

        $userId = session('user_id');
        $userRole = session('user_role');

        if ($userRole !== 'admin' && $template->user_id !== $userId) {
            abort(403);
        }

        return view('templates.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = Template::findOrFail($id);

        $userId = session('user_id');
        $userRole = session('user_role');

        if ($userRole !== 'admin' && $template->user_id !== $userId) {
            abort(403);
        }

        $validator = Validator::make($request->input(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'body_html' => 'required|string',
            'variables' => 'nullable|array',
            'is_published' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return Response::json(['errors' => $validator->errors()], 422);
            }
            session('errors', $validator->errors());
            session('_old_input', $request->input());
            redirect_back();
            return;
        }

        $data = $request->input();

        // Только админ может менять is_system
        if ($userRole !== 'admin') {
            unset($data['is_system']);
        }

        $template->update($data);

        if ($request->expectsJson()) {
            return Response::json($template);
        }

        session('success', 'Шаблон обновлен');
        redirect('/templates');
    }

    public function destroy(Request $request, $id)
    {
        $template = Template::findOrFail($id);

        $userId = session('user_id');
        $userRole = session('user_role');

        if ($userRole !== 'admin' && $template->user_id !== $userId) {
            abort(403);
        }

        $template->delete();

        if ($request->expectsJson()) {
            return Response::json(['message' => 'Deleted']);
        }

        session('success', 'Шаблон удален');
        redirect('/templates');
    }
}

