<!-- Hero секция личного кабинета -->
<div class="user-hero">
    <div class="container">
        <h1>👋 Добро пожаловать!</h1>
        <p class="user-welcome">
            <?php echo htmlspecialchars($user['name']); ?>, управляйте своими товарами и предложениями
        </p>
        <div class="user-quick-actions">
            <a href="/products/create" class="btn btn-primary">
                <span class="btn-icon">📦</span>
                Добавить товар
            </a>
            <a href="/proposals/create" class="btn btn-secondary">
                <span class="btn-icon">📄</span>
                Создать КП
            </a>
            <a href="/user/edit" class="btn btn-secondary">
                <span class="btn-icon">⚙️</span>
                Настройки
            </a>
        </div>
    </div>
</div>

<!-- Статистика пользователя -->
<div class="container">
    <div class="user-stats">
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-number"><?php echo $stats['products']['total']; ?></div>
            <div class="stat-label">Моих товаров</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📄</div>
            <div class="stat-number"><?php echo $stats['proposals']['total']; ?></div>
            <div class="stat-label">Моих предложений</div>
        </div>
    </div>
</div>

<!-- Dashboard -->
<div class="container">
    <div class="user-dashboard">
        <!-- Основной контент -->
        <div class="main-content">
            <!-- Недавняя активность -->
            <div class="recent-activity">
                <h2>📊 Недавняя активность</h2>
                <div class="activity-list">
                    <?php
                    $activities = [];

                    // Последние товары
                    if (!empty($recentProducts)) {
                        foreach (array_slice($recentProducts, 0, 2) as $product) {
                            $activities[] = [
                                'type' => 'product',
                                'icon' => '📦',
                                'title' => 'Добавлен товар: ' . htmlspecialchars($product['name']),
                                'description' => 'Цена: ' . number_format($product['price'], 0, ',', ' ') . ' ₽',
                                'time' => date('d.m.Y H:i', strtotime($product['created_at'])),
                                'link' => '/products/' . $product['id']
                            ];
                        }
                    }

                    // Последние предложения
                    if (!empty($recentProposals)) {
                        foreach (array_slice($recentProposals, 0, 2) as $proposal) {
                            $statusLabels = [
                                'draft' => 'Черновик',
                                'sent' => 'Отправлено',
                                'accepted' => 'Принято',
                                'rejected' => 'Отклонено'
                            ];
                            $activities[] = [
                                'type' => 'proposal',
                                'icon' => '📄',
                                'title' => 'Создано предложение: ' . htmlspecialchars($proposal['title']),
                                'description' => 'Сумма: ' . number_format($proposal['total'], 0, ',', ' ') . ' ₽',
                                'time' => date('d.m.Y H:i', strtotime($proposal['created_at'])),
                                'link' => '/proposals/' . $proposal['id']
                            ];
                        }
                    }

                    // Сортировка по времени (новые сверху)
                    usort($activities, function($a, $b) {
                        return strtotime($b['time']) - strtotime($a['time']);
                    });

                    // Ограничение до 4 элементов
                    $activities = array_slice($activities, 0, 4);

                    if (empty($activities)): ?>
                        <div class="activity-item">
                            <div class="activity-icon">📝</div>
                            <div class="activity-content">
                                <h4>Начните работу</h4>
                                <p>Добавьте свой первый товар или создайте коммерческое предложение</p>
                                <div class="activity-time">Добро пожаловать!</div>
                            </div>
                        </div>
                    <?php else:
                        foreach ($activities as $activity): ?>
                            <a href="<?php echo $activity['link']; ?>" class="activity-item">
                                <div class="activity-icon"><?php echo $activity['icon']; ?></div>
                                <div class="activity-content">
                                    <h4><?php echo $activity['title']; ?></h4>
                                    <p><?php echo $activity['description']; ?></p>
                                    <div class="activity-time"><?php echo $activity['time']; ?></div>
                                </div>
                            </a>
                        <?php endforeach;
                    endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Профиль пользователя -->
            <div class="user-profile">
                <div class="user-avatar">
                    <?php echo strtoupper(substr(htmlspecialchars($user['name']), 0, 1)); ?>
                </div>
                <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                <div class="user-info">
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Роль</span>
                        <span class="info-value">
                            <span class="role-badge role-<?php echo $user['role']; ?>">
                                <?php echo $user['role'] === 'admin' ? '👑 Администратор' : '👤 Пользователь'; ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Регистрация</span>
                        <span class="info-value"><?php echo date('d.m.Y', strtotime($user['created_at'] ?? 'now')); ?></span>
                    </div>
                </div>
                <div style="margin-top: 2rem;">
                    <a href="/user/edit" class="btn btn-primary" style="width: 100%;">⚙️ Настройки профиля</a>
                </div>
            </div>

            <!-- Быстрые действия -->
            <div class="quick-actions">
                <h3>🚀 Быстрые действия</h3>
                <div class="quick-actions-grid">
                    <a href="/products" class="quick-action-btn">
                        <div class="quick-action-icon">📦</div>
                        <div class="quick-action-content">
                            <h4>Мои товары</h4>
                            <p>Управление каталогом товаров</p>
                        </div>
                    </a>
                    <a href="/proposals" class="quick-action-btn">
                        <div class="quick-action-icon">📄</div>
                        <div class="quick-action-content">
                            <h4>Мои КП</h4>
                            <p>Просмотр коммерческих предложений</p>
                        </div>
                    </a>
                    <a href="/products/create" class="quick-action-btn">
                        <div class="quick-action-icon">➕</div>
                        <div class="quick-action-content">
                            <h4>Новый товар</h4>
                            <p>Добавить товар в каталог</p>
                        </div>
                    </a>
                    <a href="/proposals/create" class="quick-action-btn">
                        <div class="quick-action-icon">📝</div>
                        <div class="quick-action-content">
                            <h4>Новое КП</h4>
                            <p>Создать коммерческое предложение</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

