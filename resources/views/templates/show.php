@extends('layouts.app')

@section('title', $template->title)

@section('content')
<div class="template-view">
    <div class="template-header">
        <h1>{{ $template->title }}</h1>
        <div class="template-actions">
            @if(session('user_id') === $template->user_id || session('user_role') === 'admin')
                <a href="/templates/{{ $template->id }}/edit" class="btn">Редактировать</a>
            @endif
            <a href="/proposals/create?template_id={{ $template->id }}" class="btn btn-primary">Использовать шаблон</a>
        </div>
    </div>

    @if($template->description)
        <p class="template-description">{{ $template->description }}</p>
    @endif

    <div class="template-content">
        {!! $template->body_html !!}
    </div>

    @if($template->variables)
        <div class="template-variables">
            <h3>Переменные шаблона:</h3>
            <ul>
                @foreach($template->variables as $var)
                    <li>{{ '{' . $var . '}' }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection



