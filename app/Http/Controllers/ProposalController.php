<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\ProposalItem;
use App\Models\Template;
use App\Http\Request;
use App\Http\Response;
use App\Support\Validator;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
        $query = Proposal::with(['user', 'items']);

        // Фильтры
        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->get('author')) {
            $query->where('user_id', $request->get('author'));
        }

        if ($request->get('q')) {
            $query->where('title', 'like', '%' . $request->get('q') . '%');
        }

        // Права доступа
        $userId = session('user_id');
        $userRole = session('user_role');
        
        if ($userRole !== 'admin') {
            $query->where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhere('status', 'published');
            });
        }

        // Сортировка
        $query->orderBy('created_at', 'desc');

        // Пагинация
        $perPage = $request->get('per_page', 10);
        $proposals = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return Response::json($proposals);
        }

        return view('proposals.index', compact('proposals'));
    }

    public function create(Request $request)
    {
        $templateId = $request->get('template_id');
        $template = $templateId ? Template::find($templateId) : null;
        $templates = Template::query()
            ->where('is_published', '=', true)
            ->get();

        return view('proposals.create', compact('template', 'templates'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'title' => 'required|string|max:255',
            'offer_number' => 'nullable|string',
            'offer_date' => 'required|date',
            'seller_info' => 'nullable|string',
            'buyer_info' => 'nullable|string',
            'body_html' => 'required|string',
            'currency' => 'nullable|string|max:10',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'terms' => 'nullable|string',
            'status' => 'nullable|in:draft,published',
            'items' => 'nullable|array',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return Response::json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->input();
        $data['user_id'] = session('user_id');
        $data['status'] = $data['status'] ?? 'draft';
        
        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        $items = $data['items'] ?? [];
        unset($data['items']);

        $proposal = Proposal::create($data);

        foreach ($items as $index => $item) {
            ProposalItem::create([
                'proposal_id' => $proposal->id,
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?? 'шт.',
                'price' => $item['price'],
                'discount' => $item['discount'] ?? 0,
                'sort_order' => $index,
            ]);
        }

        if ($request->expectsJson()) {
            return Response::json($proposal->load('items'), 201);
        }

        return redirect('/proposals/' . $proposal->id)->with('success', 'КП создано');
    }

    public function show(Request $request, $id)
    {
        $proposal = Proposal::with(['user', 'items', 'template'])->findOrFail($id);

        // Проверка прав доступа
        $userId = session('user_id');
        $userRole = session('user_role');
        
        if ($userRole !== 'admin' && $proposal->user_id !== $userId && !$proposal->isPublished()) {
            abort(403);
        }

        if ($request->expectsJson()) {
            return Response::json($proposal);
        }

        return view('proposals.show', compact('proposal'));
    }

    public function edit(Request $request, $id)
    {
        $proposal = Proposal::with('items')->findOrFail($id);

        // Проверка прав
        $userId = session('user_id');
        $userRole = session('user_role');
        
        if ($userRole !== 'admin' && $proposal->user_id !== $userId) {
            abort(403);
        }

        $templates = Template::query()
            ->where('is_published', '=', true)
            ->get();

        return view('proposals.edit', compact('proposal', 'templates'));
    }

    public function update(Request $request, $id)
    {
        $proposal = Proposal::findOrFail($id);

        // Проверка прав
        $userId = session('user_id');
        $userRole = session('user_role');
        
        if ($userRole !== 'admin' && $proposal->user_id !== $userId) {
            abort(403);
        }

        $validator = Validator::make($request->input(), [
            'title' => 'required|string|max:255',
            'offer_number' => 'nullable|string',
            'offer_date' => 'required|date',
            'seller_info' => 'nullable|string',
            'buyer_info' => 'nullable|string',
            'body_html' => 'required|string',
            'currency' => 'nullable|string|max:10',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'terms' => 'nullable|string',
            'status' => 'nullable|in:draft,published',
            'items' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return Response::json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->input();
        
        if (isset($data['status']) && $data['status'] === 'published' && !$proposal->isPublished()) {
            $data['published_at'] = now();
        }

        $items = $data['items'] ?? [];
        unset($data['items']);

        $proposal->update($data);

        // Обновление позиций
        if (!empty($items)) {
            $proposal->items()->delete();
            foreach ($items as $index => $item) {
                ProposalItem::create([
                    'proposal_id' => $proposal->id,
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'шт.',
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'sort_order' => $index,
                ]);
            }
        }

        if ($request->expectsJson()) {
            return Response::json($proposal->load('items'));
        }

        return redirect('/proposals/' . $proposal->id)->with('success', 'КП обновлено');
    }

    public function destroy(Request $request, $id)
    {
        $proposal = Proposal::findOrFail($id);

        // Проверка прав
        $userId = session('user_id');
        $userRole = session('user_role');
        
        if ($userRole !== 'admin' && $proposal->user_id !== $userId) {
            abort(403);
        }

        $proposal->delete();

        if ($request->expectsJson()) {
            return Response::json(['message' => 'Deleted']);
        }

        return redirect('/proposals')->with('success', 'КП удалено');
    }

    public function publish(Request $request, $id)
    {
        $proposal = Proposal::findOrFail($id);
        $proposal->publish();

        if ($request->expectsJson()) {
            return Response::json($proposal);
        }

        return redirect()->back()->with('success', 'КП опубликовано');
    }
}

