@extends('layouts.app')

@section('title', 'Вход')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <h1>Вход</h1>
        <form method="POST" action="/login">
            <input type="hidden" name="_token" value="{{ session('_token') }}">
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required autofocus>
            </div>

            <div class="form-group">
                <label>Пароль</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary">Войти</button>
        </form>

        <div class="text-center">
            <p>Нет аккаунта? <a href="/register">Зарегистрироваться</a></p>
        </div>
    </div>

    <div class="test-accounts">
        <h3 style="text-align: center; margin-bottom: 20px; color: #4a5568;">Тестовые аккаунты</h3>

        <div class="account-card" onclick="fillForm('admin@example.com', 'password')">
            <div class="account-avatar">👑</div>
            <div class="account-info">
                <div class="account-role">Администратор</div>
                <div class="account-email">admin@example.com</div>
            </div>
            <button class="account-fill">Войти</button>
        </div>

        <div class="account-card" onclick="fillForm('user@example.com', 'password')">
            <div class="account-avatar">👤</div>
            <div class="account-info">
                <div class="account-role">Пользователь</div>
                <div class="account-email">user@example.com</div>
            </div>
            <button class="account-fill">Войти</button>
        </div>
    </div>
</div>

<script>
function fillForm(email, password) {
    document.querySelector('input[name="email"]').value = email;
    document.querySelector('input[name="password"]').value = password;
    // Автоматически отправляем форму через 500ms
    setTimeout(() => {
        document.querySelector('form').submit();
    }, 500);
}
</script>
@endsection



