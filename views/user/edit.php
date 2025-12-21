<div class="page-header">
    <h1>Редактирование профиля</h1>
    <a href="/user" class="btn btn-secondary">← Личный кабинет</a>
</div>

<form method="POST" action="/user" class="product-form">
    <div class="form-section">
        <h2>Информация профиля</h2>

        <div class="form-row">
            <div class="form-group">
                <label for="name">Имя *</label>
                <input type="text" id="name" name="name" required
                       class="form-input" value="<?php echo htmlspecialchars($user['name']); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required
                       class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>
        </div>

        <h2 style="margin-top: 2rem;">Изменение пароля</h2>
        <p style="color: #b0b0b0; margin-bottom: 1rem;">Оставьте поля пустыми, если не хотите менять пароль</p>

        <div class="form-row">
            <div class="form-group">
                <label for="password">Новый пароль</label>
                <input type="password" id="password" name="password"
                       class="form-input" placeholder="Минимум 6 символов">
            </div>
            <div class="form-group">
                <label for="password_confirmation">Подтверждение пароля</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="form-input" placeholder="Повторите новый пароль">
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        <a href="/user" class="btn btn-secondary">Отмена</a>
    </div>
</form>

<script>
// Проверка совпадения паролей
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');

    function checkPasswords() {
        if (password.value && confirmation.value && password.value !== confirmation.value) {
            confirmation.setCustomValidity('Пароли не совпадают');
        } else {
            confirmation.setCustomValidity('');
        }
    }

    password.addEventListener('input', checkPasswords);
    confirmation.addEventListener('input', checkPasswords);
});
</script>
