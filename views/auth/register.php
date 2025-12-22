<div class="auth-container">
    <div class="auth-card">
        <h1>Регистрация</h1>

        <!-- Предупреждение о безопасности -->
        <div class="security-warning">
            <strong>⚠️ Предупреждение безопасности:</strong>
            Этот сайт использует незащищенное соединение (HTTP). Ваши данные могут быть перехвачены.
            Для безопасного использования рекомендуется использовать HTTPS.
        </div>

        <div id="error-message" class="error-message" style="display: none;"></div>
        <div id="success-message" class="success-message" style="display: none;"></div>

        <form id="register-form" class="auth-form">
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

<script>
document.getElementById('register-form').addEventListener('submit', async function(e) {
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
    submitBtn.textContent = 'Регистрация...';

    try {
        const response = await fetch('/register', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Успешная регистрация
            successDiv.textContent = data.message;
            successDiv.style.display = 'block';

            // Перенаправляем через 2 секунды
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 2000);
        } else {
            // Ошибка регистрации
            errorDiv.textContent = data.error || 'Произошла ошибка';
            errorDiv.style.display = 'block';
        }
    } catch (error) {
        errorDiv.textContent = 'Ошибка сети. Попробуйте еще раз.';
        errorDiv.style.display = 'block';
    } finally {
        // Включаем кнопку обратно
        submitBtn.disabled = false;
        submitBtn.textContent = 'Зарегистрироваться';
    }
});
</script>
