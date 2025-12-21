<div class="auth-container">
    <div class="auth-card">
        <h1>Вход в систему</h1>

        <form method="POST" action="/login" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required
                       class="form-input" placeholder="your@email.com">
            </div>

            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required
                       class="form-input" placeholder="Ваш пароль">
            </div>

            <button type="submit" class="btn btn-primary btn-full">Войти</button>
        </form>

        <div class="auth-links">
            <p>Нет аккаунта? <a href="/register">Зарегистрироваться</a></p>
        </div>
    </div>
</div>
