@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <h1>Регистрация</h1>
        <form method="POST" action="/register">
            <input type="hidden" name="_token" value="{{ session('_token') }}">
            
            <div class="form-group">
                <label>Имя</label>
                <input type="text" name="name" required autofocus>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Пароль</label>
                <input type="password" name="password" required minlength="8">
            </div>

            <div class="form-group">
                <label>Подтверждение пароля</label>
                <input type="password" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
        </form>

        <p class="text-center">
            Уже есть аккаунт? <a href="/login">Войти</a>
        </p>
    </div>
</div>
@endsection



