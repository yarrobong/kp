<!-- Приветственный хедер -->
<div class="dashboard-header">
    <div class="welcome-section">
        <div class="welcome-avatar">
            <div class="avatar-circle"><?php echo strtoupper(substr(htmlspecialchars($user['name']), 0, 1)); ?></div>
        </div>
        <div class="welcome-content">
            <h1>Добро пожаловать, <?php echo htmlspecialchars($user['name']); ?>! 👋</h1>
            <p class="welcome-subtitle">Ваш личный дашборд управления товарами и предложениями</p>
            <div class="welcome-actions">
                <a href="/products/create" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Добавить товар
                </a>
                <a href="/proposals/create" class="btn btn-success">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 13H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 17H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Создать КП
                </a>
                <?php if ($user['role'] === 'admin'): ?>
                <a href="/admin" class="btn btn-warning">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Админ панель
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Быстрые действия -->
<div class="quick-actions">
    <h2>Быстрые действия</h2>
    <div class="actions-grid">
        <a href="/products/create" class="action-card">
            <div class="action-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M7 10L9 12L13 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="16" cy="10" r="2" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
            <div class="action-content">
                <h3>Добавить товар</h3>
                <p>Создайте новый товар в каталоге</p>
            </div>
        </a>

        <a href="/proposals/create" class="action-card">
            <div class="action-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 13H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 17H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10 9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="action-content">
                <h3>Создать предложение</h3>
                <p>Сформируйте коммерческое предложение</p>
            </div>
        </a>

        <a href="/products" class="action-card">
            <div class="action-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 7L12 13L3 7M3 7H21M3 7L3 17C3 17.5304 3.21071 18.0391 3.58579 18.4142C3.96086 18.7893 4.46957 19 5 19H19C19.5304 19 20.0391 18.7893 20.4142 18.4142C20.7893 18.0391 21 17.5304 21 17V7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="action-content">
                <h3>Управление товарами</h3>
                <p>Просмотр и редактирование каталога</p>
            </div>
        </a>

        <a href="/proposals" class="action-card">
            <div class="action-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12H15M9 16H15M17 8H7C5.89543 8 5 8.89543 5 10V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V10C19 8.89543 18.1046 8 17 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5 10V6C5 4.89543 5.89543 4 7 4H17C18.1046 4 19 4.89543 19 6V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="action-content">
                <h3>Мои предложения</h3>
                <p>Управление коммерческими предложениями</p>
            </div>
        </a>
    </div>
</div>

<!-- Статистика -->
<div class="stats-overview">
    <h2>📊 Статистика</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 7L12 13L4 7M4 7H20M4 7L4 17C4 17.5304 4.21071 18.0391 4.58579 18.4142C4.96086 18.7893 4.46957 19 5 19H19C19.5304 19 20.0391 18.7893 20.4142 18.4142C20.7893 18.0391 21 17.5304 21 17V7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['products']['total']; ?></div>
                <div class="stat-label">Всего товаров</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12H15M9 16H15M17 8H7C5.89543 8 5 8.89543 5 10V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V10C19 8.89543 18.1046 8 17 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5 10V6C5 4.89543 5.89543 4 7 4H17C18.1046 4 19 4.89543 19 6V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['proposals']['total']; ?></div>
                <div class="stat-label">Всего предложений</div>
            </div>
        </div>

        <?php if (!empty($stats['proposals']['by_status'])): ?>
        <div class="stat-card">
            <div class="stat-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['proposals']['by_status']['accepted'] ?? 0; ?></div>
                <div class="stat-label">Принятых КП</div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Профиль -->
<div class="profile-section">
    <h2>Профиль</h2>
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                <div class="avatar-large"><?php echo strtoupper(substr(htmlspecialchars($user['name']), 0, 1)); ?></div>
            </div>
            <div class="profile-info">
                <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
                <span class="role-badge role-<?php echo $user['role']; ?>">
                    <?php echo $user['role'] === 'admin' ? 'Администратор' : 'Пользователь'; ?>
                </span>
            </div>
        </div>
        <div class="profile-actions">
            <a href="/user/edit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18.5 2.49998C18.8978 2.10216 19.4374 1.87866 20 1.87866C20.5626 1.87866 21.1022 2.10216 21.5 2.49998C21.8978 2.89781 22.1213 3.43737 22.1213 3.99998C22.1213 4.56259 21.8978 5.10216 21.5 5.49998L12 15L8 16L9 12L18.5 2.49998Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Редактировать профиль
            </a>
        </div>
    </div>
</div>

<!-- Недавняя активность -->
<div class="recent-activity">
    <h2>📈 Недавняя активность</h2>
    <div class="activity-tabs">
        <div class="activity-content">
            <?php if (!empty($recentProducts) || !empty($recentProposals)): ?>
                <div class="activity-grid">
                    <?php
                    // Объединяем товары и предложения для отображения
                    $activities = [];

                    // Добавляем товары
                    foreach ($recentProducts as $product) {
                        $activities[] = [
                            'type' => 'product',
                            'data' => $product,
                            'date' => strtotime($product['created_at'] ?? 'now')
                        ];
                    }

                    // Добавляем предложения
                    foreach ($recentProposals as $proposal) {
                        $activities[] = [
                            'type' => 'proposal',
                            'data' => $proposal,
                            'date' => strtotime($proposal['created_at'] ?? 'now')
                        ];
                    }

                    // Сортируем по дате (новые сначала)
                    usort($activities, function($a, $b) {
                        return $b['date'] - $a['date'];
                    });

                    // Ограничиваем до 6 элементов
                    $activities = array_slice($activities, 0, 6);
                    ?>

                    <?php foreach ($activities as $activity): ?>
                        <?php if ($activity['type'] === 'product'): ?>
                            <?php $product = $activity['data']; ?>
                            <div class="activity-item">
                                <div class="activity-icon product-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20 7L12 13L4 7M4 7H20M4 7L4 17C4 17.5304 4.21071 18.0391 4.58579 18.4142C4.96086 18.7893 4.46957 19 5 19H19C19.5304 19 20.0391 18.7893 20.4142 18.4142C20.7893 18.0391 21 17.5304 21 17V7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">Добавлен товар</div>
                                    <div class="activity-description"><?php echo htmlspecialchars($product['name']); ?></div>
                                    <div class="activity-meta"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</div>
                                </div>
                                <div class="activity-actions">
                                    <a href="/products/<?php echo $product['id']; ?>" class="btn btn-small">Просмотр</a>
                                </div>
                            </div>
                        <?php elseif ($activity['type'] === 'proposal'): ?>
                            <?php $proposal = $activity['data']; ?>
                            <div class="activity-item">
                                <div class="activity-icon proposal-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">Создано предложение</div>
                                    <div class="activity-description"><?php echo htmlspecialchars($proposal['offer_number']); ?></div>
                                    <div class="activity-meta"><?php echo number_format($proposal['total'], 0, ',', ' '); ?> ₽</div>
                                </div>
                                <div class="activity-actions">
                                    <a href="/proposals/<?php echo $proposal['id']; ?>" class="btn btn-small">Просмотр</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-activity">
                    <div class="empty-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12H15M9 16H15M17 8H7C5.89543 8 5 8.89543 5 10V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V10C19 8.89543 18.1046 8 17 8Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5 10V6C5 4.89543 5.89543 4 7 4H17C18.1046 4 19 4.89543 19 6V10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3>Активность отсутствует</h3>
                    <p>Начните работу, добавив товар или создав коммерческое предложение</p>
                    <div class="empty-actions">
                        <a href="/products/create" class="btn btn-primary">Добавить товар</a>
                        <a href="/proposals/create" class="btn btn-secondary">Создать КП</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Советы по использованию -->
<div class="tips-section">
    <h2>💡 Советы по работе</h2>
    <div class="tips-grid">
        <div class="tip-card">
            <div class="tip-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M7 10L9 12L13 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="16" cy="10" r="2" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
            <h3>Добавляйте качественные фото</h3>
            <p>Хорошие изображения товаров повышают доверие клиентов и увеличивают конверсию</p>
        </div>

        <div class="tip-card">
            <div class="tip-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12H15M9 16H15M17 8H7C5.89543 8 5 8.89543 5 10V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V10C19 8.89543 18.1046 8 17 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5 10V6C5 4.89543 5.89543 4 7 4H17C18.1046 4 19 4.89543 19 6V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h3>Используйте шаблоны КП</h3>
            <p>Стандартизируйте предложения для разных типов клиентов и товаров</p>
        </div>

        <div class="tip-card">
            <div class="tip-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h3>Отслеживайте статусы</h3>
            <p>Регулярно обновляйте статусы предложений для эффективного управления</p>
        </div>

        <div class="tip-card">
            <div class="tip-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h3>Экспортируйте в PDF</h3>
            <p>Готовые PDF документы выглядят профессионально и готовы к отправке</p>
        </div>
    </div>
</div>
