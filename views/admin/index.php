<div class="page-header">
    <h1>Админ панель</h1>
    <a href="/" class="btn btn-secondary">← На главную</a>
</div>

<!-- Статистика системы -->
<div class="admin-stats">
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats['users'] ?? 0; ?></div>
        <div class="stat-label">Пользователей</div>
    </div>

    <div class="stat-card">
        <div class="stat-number"><?php echo $stats['admins'] ?? 0; ?></div>
        <div class="stat-label">Администраторов</div>
    </div>

    <div class="stat-card">
        <div class="stat-number"><?php echo $stats['products'] ?? 0; ?></div>
        <div class="stat-label">Товаров</div>
    </div>

    <div class="stat-card">
        <div class="stat-number"><?php echo $stats['proposals'] ?? 0; ?></div>
        <div class="stat-label">Предложений</div>
    </div>
</div>

<!-- Предложения по статусам -->
<?php if (!empty($stats['proposals_by_status'])): ?>
<div class="user-section">
    <h2>Предложения по статусам</h2>
    <div class="admin-stats">
        <?php foreach ($stats['proposals_by_status'] as $status => $count): ?>
        <div class="stat-card">
            <div class="stat-number"><?php echo $count; ?></div>
            <div class="stat-label">
                <?php
                $statusLabels = [
                    'draft' => 'Черновики',
                    'sent' => 'Отправлены',
                    'accepted' => 'Приняты',
                    'rejected' => 'Отклонены'
                ];
                echo $statusLabels[$status] ?? $status;
                ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Быстрые действия -->
<div class="user-section">
    <h2>Управление</h2>
    <div class="admin-actions">
        <a href="/admin/users" class="btn btn-primary">Управление пользователями</a>
    </div>
</div>
