@extends('layouts.app')

@section('title', 'Шаблоны')

@section('content')
<div class="page-header">
    <h1>Шаблоны КП</h1>
    <a href="/templates/create" class="btn btn-primary">Создать шаблон</a>
</div>

<div class="templates-list">
    @forelse($templates as $template)
        <div class="template-card">
            <div class="template-header">
                <h3><a href="/templates/{{ $template->id }}">{{ $template->title }}</a></h3>
                @if($template->is_system)
                    <span class="badge badge-info">Системный</span>
                @endif
                @if($template->is_published)
                    <span class="badge badge-success">Опубликован</span>
                @endif
            </div>
            @if($template->description)
                <p>{{ $template->description }}</p>
            @endif
            <div class="template-actions">
                <a href="/templates/{{ $template->id }}" class="btn btn-sm">Просмотр</a>
                @if(session('user_id') === $template->user_id || session('user_role') === 'admin')
                    <a href="/templates/{{ $template->id }}/edit" class="btn btn-sm">Редактировать</a>
                    <a href="/proposals/create?template_id={{ $template->id }}" class="btn btn-sm btn-primary">Использовать</a>
                    <form method="POST" action="/templates/{{ $template->id }}" style="display: inline;">
                        <input type="hidden" name="_token" value="{{ session('_token') }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">Удалить</button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <p>Нет шаблонов. <a href="/templates/create">Создать первый шаблон</a></p>
    @endforelse
</div>
@endsection



