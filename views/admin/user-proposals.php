<div class="page-header">
    <h1>Предложения пользователя: <?php echo htmlspecialchars($user['name']); ?></h1>
    <div>
        <a href="/admin/users/<?php echo $user['id']; ?>/products" class="btn btn-secondary">Товары пользователя</a>
        <a href="/admin/users" class="btn btn-secondary">← Все пользователи</a>
    </div>
</div>

<div class="user-section">
    <h2>Информация о пользователе</h2>
    <div class="user-info">
        <div class="info-row">
            <span class="info-label">Имя:</span>
            <span class="info-value"><?php echo htmlspecialchars($user['name']); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Роль:</span>
            <span class="info-value">
                <span class="role-badge role-<?php echo $user['role']; ?>">
                    <?php echo $user['role'] === 'admin' ? 'Администратор' : 'Пользователь'; ?>
                </span>
            </span>
        </div>
    </div>
</div>

<div class="user-section">
    <h2>Коммерческие предложения (<?php echo count($proposals); ?>)</h2>

    <?php if (empty($proposals)): ?>
    <div class="empty-state">
        <h3>У пользователя нет предложений</h3>
        <p>Пользователь еще не создал ни одного коммерческого предложения.</p>
    </div>
    <?php else: ?>
    <div class="proposals-list">
        <?php foreach ($proposals as $proposal): ?>
        <div class="proposal-card">
            <div class="proposal-header">
                <h3><?php echo htmlspecialchars($proposal['title']); ?></h3>
                <span class="status-badge status-<?php echo htmlspecialchars($proposal['status']); ?>">
                    <?php
                    $statusLabels = [
                        'draft' => 'Черновик',
                        'sent' => 'Отправлено',
                        'accepted' => 'Принято',
                        'rejected' => 'Отклонено'
                    ];
                    echo $statusLabels[$proposal['status']] ?? $proposal['status'];
                    ?>
                </span>
            </div>

            <div class="proposal-meta">
                <span>Номер: <?php echo htmlspecialchars($proposal['offer_number']); ?></span>
                <span>Дата: <?php echo htmlspecialchars($proposal['offer_date']); ?></span>
                <span>Сумма: <?php echo number_format($proposal['total'], 0, ',', ' '); ?> ₽</span>
            </div>

            <div class="proposal-actions">
                <a href="/proposals/<?php echo $proposal['id']; ?>" class="btn btn-small">Просмотр</a>
                <a href="/proposals/<?php echo $proposal['id']; ?>/edit" class="btn btn-small btn-secondary">Редактировать</a>
                <a href="/proposals/<?php echo $proposal['id']; ?>/pdf" class="btn btn-small btn-success">PDF</a>
                <a href="/proposals/<?php echo $proposal['id']; ?>/delete" class="btn btn-small btn-danger"
                   onclick="return confirm('Удалить предложение?')">Удалить</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
