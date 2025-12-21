<div class="page-header">
    <h1>Товары пользователя: <?php echo htmlspecialchars($user['name']); ?></h1>
    <div>
        <a href="/admin/users/<?php echo $user['id']; ?>/proposals" class="btn btn-secondary">Предложения пользователя</a>
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
    <h2>Товары пользователя (<?php echo count($products); ?>)</h2>

    <?php if (empty($products)): ?>
    <div class="empty-state">
        <h3>У пользователя нет товаров</h3>
        <p>Пользователь еще не добавил ни одного товара.</p>
    </div>
    <?php else: ?>
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
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
                <a href="/products/<?php echo $product['id']; ?>/delete" class="btn btn-small btn-danger"
                   onclick="return confirm('Удалить товар?')">Удалить</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
