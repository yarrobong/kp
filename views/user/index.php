<div class="page-header">
    <h1>Личный кабинет</h1>
    <a href="/" class="btn btn-secondary">← На главную</a>
</div>

<!-- Статистика пользователя -->
<div class="user-stats">
    <div class="stat-card">
        <div class="stat-number"><?php echo $stats['products']['total']; ?></div>
        <div class="stat-label">Моих товаров</div>
    </div>

    <div class="stat-card">
        <div class="stat-number"><?php echo $stats['proposals']['total']; ?></div>
        <div class="stat-label">Моих предложений</div>
    </div>
</div>

<!-- Информация о пользователе -->
<div class="user-section">
    <h2>Информация о профиле</h2>
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
    <div style="margin-top: 2rem;">
        <a href="/user/edit" class="btn btn-primary">Редактировать профиль</a>
    </div>
</div>

<!-- Недавние товары -->
<?php if (!empty($recentProducts)): ?>
<div class="user-section">
    <h2>Недавние товары</h2>
    <div class="products-grid">
        <?php foreach ($recentProducts as $product): ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image']); ?>"
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-info">
                <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                <div class="product-meta">
                    <span class="product-price"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</span>
                    <?php if (!empty($product['category'])): ?>
                    <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="product-actions">
                <a href="/products/<?php echo $product['id']; ?>" class="btn btn-small">Просмотр</a>
                <a href="/products/<?php echo $product['id']; ?>/edit" class="btn btn-small btn-secondary">Редактировать</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div style="margin-top: 2rem; text-align: center;">
        <a href="/user/products" class="btn btn-secondary">Все товары</a>
    </div>
</div>
<?php endif; ?>

<!-- Недавние предложения -->
<?php if (!empty($recentProposals)): ?>
<div class="user-section">
    <h2>Недавние предложения</h2>
    <div class="proposals-list">
        <?php foreach ($recentProposals as $proposal): ?>
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
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div style="margin-top: 2rem; text-align: center;">
        <a href="/user/proposals" class="btn btn-secondary">Все предложения</a>
    </div>
</div>
<?php endif; ?>
