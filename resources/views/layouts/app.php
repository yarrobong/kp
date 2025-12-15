<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Коммерческие предложения')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">КП Генератор</a>
            <div class="navbar-menu">
                @if(session('user_id'))
                    <a href="/proposals">Мои КП</a>
                    <a href="/templates">Шаблоны</a>
                    @if(session('user_role') === 'admin')
                        <a href="/admin">Админка</a>
                    @endif
                    <form method="POST" action="/logout" style="display: inline;">
                        <input type="hidden" name="_token" value="{{ session('_token') }}">
                        <button type="submit" class="btn-link">Выход</button>
                    </form>
                @else
                    <a href="/login">Вход</a>
                    <a href="/register">Регистрация</a>
                @endif
            </div>
        </div>
    </nav>

    <main class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if(isset($errors) && $errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>



