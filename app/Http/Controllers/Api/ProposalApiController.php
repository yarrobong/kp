<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Http\Request;
use App\Http\Response;

class ProposalApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Proposal::with(['user', 'items']);

        // Пользователь видит только свои КП (кроме админа)
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        // Фильтры
        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->get('q')) {
            $query->where('title', 'like', '%' . $request->get('q') . '%');
        }

        $perPage = $request->get('per_page', 10);
        $proposals = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return Response::json($proposals);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $proposal = Proposal::create([
            'user_id' => $user->id,
            'title' => $request->input('title'),
            'offer_number' => $request->input('offer_number'),
            'offer_date' => $request->input('offer_date'),
            'seller_info' => $request->input('seller_info'),
            'buyer_info' => $request->input('buyer_info'),
            'body_html' => $request->input('body_html'),
            'currency' => $request->input('currency', '₽'),
            'vat_rate' => $request->input('vat_rate', 0),
            'terms' => $request->input('terms'),
            'status' => $request->input('status', 'draft'),
        ]);

        return Response::json($proposal->load('items'), 201);
    }

    public function show(Request $request, $id)
    {
        $proposal = Proposal::with(['user', 'items'])->findOrFail($id);
        $user = $request->user();

        if (!$user->isAdmin() && $proposal->user_id !== $user->id) {
            return Response::json(['error' => 'Forbidden'], 403);
        }

        return Response::json($proposal);
    }

    public function update(Request $request, $id)
    {
        $proposal = Proposal::findOrFail($id);
        $user = $request->user();

        if (!$user->isAdmin() && $proposal->user_id !== $user->id) {
            return Response::json(['error' => 'Forbidden'], 403);
        }

        $proposal->update($request->input());

        return Response::json($proposal->load('items'));
    }

    public function destroy(Request $request, $id)
    {
        $proposal = Proposal::findOrFail($id);
        $user = $request->user();

        if (!$user->isAdmin() && $proposal->user_id !== $user->id) {
            return Response::json(['error' => 'Forbidden'], 403);
        }

        $proposal->delete();

        return Response::json(['message' => 'Deleted']);
    }
}



