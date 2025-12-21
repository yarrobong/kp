<div class="page-header">
    <h1>Каталог товаров</h1>
    <?php if ($user): ?>
    <a href="/products/create" class="btn btn-primary">Добавить товар</a>
    <?php endif; ?>
</div>

<!-- Поиск и фильтры -->
<div class="filters-section">
    <form method="GET" class="search-form">
        <div class="search-input">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                   placeholder="Поиск товаров..." class="form-input">
            <button type="submit" class="btn btn-secondary">Поиск</button>
        </div>
    </form>

    <?php if ($search || $category): ?>
    <div class="active-filters">
        <span>Активные фильтры:</span>
        <?php if ($search): ?>
        <span class="filter-tag">Поиск: "<?php echo htmlspecialchars($search); ?>"</span>
        <?php endif; ?>
        <a href="/products" class="btn btn-small">Сбросить</a>
    </div>
    <?php endif; ?>
</div>

<!-- Список товаров -->
<div class="products-grid">
    <?php if (empty($products)): ?>
    <div class="empty-state">
        <h3>Товары не найдены</h3>
        <p><?php echo $search ? 'Попробуйте изменить поисковый запрос' : 'Добавьте первый товар в каталог'; ?></p>
        <a href="/products/create" class="btn btn-primary">Добавить товар</a>
    </div>
    <?php else: ?>
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
            <?php if ($user && ($user['role'] === 'admin' || $product['user_id'] == $user['id'])): ?>
            <a href="/products/<?php echo $product['id']; ?>/edit" class="btn btn-small btn-secondary">Редактировать</a>
            <a href="/products/<?php echo $product['id']; ?>/delete" class="btn btn-small btn-danger"
               onclick="return confirm('Вы уверены, что хотите удалить этот товар?')">Удалить</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
