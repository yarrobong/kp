<div class="auth-container">
    <div class="auth-card">
        <h1>Вход в систему</h1>

        <!-- Предупреждение о безопасности -->
        <div class="security-warning">
            <strong>⚠️ Предупреждение безопасности:</strong>
            Этот сайт использует незащищенное соединение (HTTP). Ваши учетные данные могут быть перехвачены.
            Для безопасного использования рекомендуется использовать HTTPS.
        </div>

        <div id="error-message" class="error-message" style="display: none;"></div>
        <div id="success-message" class="success-message" style="display: none;"></div>

        <form id="login-form" class="auth-form">
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

<script>
document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const errorDiv = document.getElementById('error-message');
    const successDiv = document.getElementById('success-message');
    const submitBtn = this.querySelector('button[type="submit"]');

    // Скрываем предыдущие сообщения
    errorDiv.style.display = 'none';
    successDiv.style.display = 'none';

    // Отключаем кнопку
    submitBtn.disabled = true;
    submitBtn.textContent = 'Вход...';

    try {
        const response = await fetch('/login', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Успешная авторизация
            successDiv.textContent = data.message;
            successDiv.style.display = 'block';

            // Перенаправляем через 1 секунду
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            // Ошибка авторизации
            errorDiv.textContent = data.error || 'Произошла ошибка';
            errorDiv.style.display = 'block';
        }
    } catch (error) {
        errorDiv.textContent = 'Ошибка сети. Попробуйте еще раз.';
        errorDiv.style.display = 'block';
    } finally {
        // Включаем кнопку обратно
        submitBtn.disabled = false;
        submitBtn.textContent = 'Войти';
    }
});
</script>
