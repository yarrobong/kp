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

        <p class="text-center">
            Нет аккаунта? <a href="/register">Зарегистрироваться</a>
        </p>
    </div>
</div>
@endsection



