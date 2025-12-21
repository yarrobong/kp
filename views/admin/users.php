<div class="page-header">
    <h1>Управление пользователями</h1>
    <div>
        <a href="/admin" class="btn btn-secondary">← Админ панель</a>
        <a href="/register" class="btn btn-primary">Добавить пользователя</a>
    </div>
</div>

<div class="user-section">
    <h2>Все пользователи системы (<?php echo count($users); ?>)</h2>

    <div class="users-grid">
        <?php foreach ($users as $user): ?>
        <div class="user-card">
            <div class="user-header">
                <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                <span class="role-badge role-<?php echo $user['role']; ?>">
                    <?php echo $user['role'] === 'admin' ? 'Администратор' : 'Пользователь'; ?>
                </span>
            </div>

            <div class="user-info">
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">ID:</span>
                    <span class="info-value"><?php echo $user['id']; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Регистрация:</span>
                    <span class="info-value"><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></span>
                </div>
            </div>

            <div class="user-actions">
                <!-- Изменение роли -->
                <form method="POST" action="/admin/users/<?php echo $user['id']; ?>/role" style="display: inline;">
                    <select name="role" class="role-select" onchange="this.form.submit()">
                        <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>Пользователь</option>
                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Администратор</option>
                    </select>
                </form>

                <!-- Действия -->
                <a href="/admin/users/<?php echo $user['id']; ?>/products" class="btn btn-small">Товары</a>
                <a href="/admin/users/<?php echo $user['id']; ?>/proposals" class="btn btn-small btn-secondary">КП</a>

                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                <form method="POST" action="/admin/users/<?php echo $user['id']; ?>/delete" style="display: inline;"
                      onsubmit="return confirm('Вы уверены, что хотите удалить пользователя <?php echo htmlspecialchars($user['name']); ?>? Это действие нельзя отменить.')">
                    <button type="submit" class="btn btn-small btn-danger">Удалить</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
