@extends('layouts.app')

@section('title', 'Управление пользователями')

@section('content')
<h1>Управление пользователями</h1>

<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Роль</th>
            <th>Дата регистрации</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <form method="POST" action="/admin/users/{{ $user->id }}/role" style="display: inline;">
                        <input type="hidden" name="_token" value="{{ session('_token') }}">
                        <select name="role" onchange="this.form.submit()">
                            <option value="guest" {{ $user->role === 'guest' ? 'selected' : '' }}>Гость</option>
                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Пользователь</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Администратор</option>
                        </select>
                    </form>
                </td>
                <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                <td>
                    <a href="/proposals?author={{ $user->id }}">КП пользователя</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@if(method_exists($users, 'links'))
    <div class="pagination">
        {{ $users->links() }}
    </div>
@endif
@endsection



