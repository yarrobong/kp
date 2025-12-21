<div class="page-header">
    <h1>Управление пользователями</h1>
    <a href="/admin" class="btn btn-secondary">← Админ панель</a>
</div>

<div class="user-section">
    <table class="users-table">
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
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                    <form method="POST" action="/admin/users/<?php echo $user['id']; ?>/role" style="display: inline;">
                        <select name="role" class="role-select" onchange="this.form.submit()">
                            <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>Пользователь</option>
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Администратор</option>
                        </select>
                    </form>
                </td>
                <td><?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?></td>
                <td>
                    <a href="/admin/users/<?php echo $user['id']; ?>/products" class="btn btn-small">Товары</a>
                    <a href="/admin/users/<?php echo $user['id']; ?>/proposals" class="btn btn-small btn-secondary">КП</a>
                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                    <form method="POST" action="/admin/users/<?php echo $user['id']; ?>/delete" style="display: inline;"
                          onsubmit="return confirm('Вы уверены, что хотите удалить пользователя <?php echo htmlspecialchars($user['name']); ?>?')">
                        <button type="submit" class="btn btn-small btn-danger">Удалить</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
