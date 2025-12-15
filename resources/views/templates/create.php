@extends('layouts.app')

@section('title', 'Создать шаблон')

@push('styles')
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
@endpush

@section('content')
<h1>Создать шаблон</h1>

<form method="POST" action="/templates">
    <input type="hidden" name="_token" value="{{ session('_token') }}">
    
    <div class="form-group">
        <label>Название шаблона *</label>
        <input type="text" name="title" required>
    </div>

    <div class="form-group">
        <label>Описание</label>
        <textarea name="description" rows="3"></textarea>
    </div>

    <div class="form-group">
        <label>Содержание шаблона *</label>
        <p class="hint">Используйте переменные вида {company_name}, {price} и т.д.</p>
        <div id="editor" style="height: 400px;"></div>
        <textarea name="body_html" id="body_html" style="display: none;" required></textarea>
    </div>

    @if(session('user_role') === 'admin')
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_system" value="1">
                Системный шаблон
            </label>
        </div>
    @endif

    <div class="form-group">
        <label>
            <input type="checkbox" name="is_published" value="1">
            Опубликовать
        </label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/templates" class="btn btn-secondary">Отмена</a>
    </div>
</form>

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    const quill = new Quill('#editor', {
        theme: 'snow'
    });

    document.querySelector('form').addEventListener('submit', function() {
        document.getElementById('body_html').value = quill.root.innerHTML;
    });
</script>
@endpush
@endsection



