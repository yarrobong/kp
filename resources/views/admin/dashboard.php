@extends('layouts.app')

@section('title', 'Админ-панель')

@section('content')
<h1>Админ-панель</h1>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Пользователи</h3>
        <p class="stat-number">{{ $stats['users'] }}</p>
    </div>
    <div class="stat-card">
        <h3>Коммерческие предложения</h3>
        <p class="stat-number">{{ $stats['proposals'] }}</p>
    </div>
    <div class="stat-card">
        <h3>Шаблоны</h3>
        <p class="stat-number">{{ $stats['templates'] }}</p>
    </div>
    <div class="stat-card">
        <h3>Опубликованные КП</h3>
        <p class="stat-number">{{ $stats['published_proposals'] }}</p>
    </div>
</div>

<div class="admin-links">
    <a href="/admin/users" class="btn btn-primary">Управление пользователями</a>
    <a href="/proposals" class="btn">Все КП</a>
    <a href="/templates" class="btn">Все шаблоны</a>
</div>
@endsection



