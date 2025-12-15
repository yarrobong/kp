@extends('layouts.app')

@section('title', 'Мои коммерческие предложения')

@section('content')
<div class="page-header">
    <h1>Коммерческие предложения</h1>
    <a href="/proposals/create" class="btn btn-primary">Создать КП</a>
</div>

<div class="filters">
    <form method="GET" action="/proposals" class="filter-form">
        <select name="status">
            <option value="">Все статусы</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Черновики</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Опубликованные</option>
        </select>
        <input type="text" name="q" placeholder="Поиск..." value="{{ request('q') }}">
        <select name="per_page">
            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        </select>
        <button type="submit" class="btn">Применить</button>
    </form>
</div>

<div class="proposals-list">
    @forelse($proposals as $proposal)
        <div class="proposal-card">
            <div class="proposal-header">
                <h3><a href="/proposals/{{ $proposal->id }}">{{ $proposal->title }}</a></h3>
                <span class="badge badge-{{ $proposal->status === 'published' ? 'success' : 'secondary' }}">
                    {{ $proposal->status === 'published' ? 'Опубликовано' : 'Черновик' }}
                </span>
            </div>
            <div class="proposal-meta">
                <span>Дата: {{ $proposal->offer_date->format('d.m.Y') }}</span>
                @if($proposal->offer_number)
                    <span>Номер: {{ $proposal->offer_number }}</span>
                @endif
            </div>
            <div class="proposal-actions">
                <a href="/proposals/{{ $proposal->id }}" class="btn btn-sm">Просмотр</a>
                <a href="/proposals/{{ $proposal->id }}/edit" class="btn btn-sm">Редактировать</a>
                <a href="/proposals/{{ $proposal->id }}/pdf" class="btn btn-sm" target="_blank">PDF</a>
                <form method="POST" action="/proposals/{{ $proposal->id }}" style="display: inline;">
                    <input type="hidden" name="_token" value="{{ session('_token') }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">Удалить</button>
                </form>
            </div>
        </div>
    @empty
        <p>Нет коммерческих предложений. <a href="/proposals/create">Создать первое КП</a></p>
    @endforelse
</div>

@if(method_exists($proposals, 'links'))
    <div class="pagination">
        {{ $proposals->links() }}
    </div>
@endif
@endsection



