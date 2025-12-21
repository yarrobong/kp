<div class="auth-container">
    <div class="auth-card">
        <h1>Регистрация</h1>

        <form method="POST" action="/register" class="auth-form">
            <div class="form-group">
                <label for="name">Имя</label>
                <input type="text" id="name" name="name" required
                       class="form-input" placeholder="Ваше имя">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required
                       class="form-input" placeholder="your@email.com">
            </div>

            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required
                       class="form-input" placeholder="Минимум 6 символов">
            </div>

            <div class="form-group">
                <label for="password_confirmation">Подтверждение пароля</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="form-input" placeholder="Повторите пароль">
            </div>

            <button type="submit" class="btn btn-primary btn-full">Зарегистрироваться</button>
        </form>

        <div class="auth-links">
            <p>Уже есть аккаунт? <a href="/login">Войти</a></p>
        </div>
    </div>
</div>
