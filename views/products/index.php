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
    <div class="product-card fade-in">
        <div class="product-image-container">
            <?php if (!empty($product['image']) && $product['image'] !== '/css/placeholder-product.svg'): ?>
                <div class="product-image">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>"
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
            <?php else: ?>
                <div class="product-placeholder">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M7 10L9 12L13 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="16" cy="10" r="2" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>Нет фото</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="product-info">
            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>

            <?php if (!empty($product['category'])): ?>
            <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
            <?php endif; ?>

            <div class="product-description">
                <?php
                $description = htmlspecialchars($product['description'] ?? '');
                echo strlen($description) > 120 ? substr($description, 0, 120) . '...' : $description;
                ?>
            </div>

            <div class="product-meta">
                <div class="product-price"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</div>
            </div>
        </div>

        <div class="product-actions">
            <a href="/products/<?php echo $product['id']; ?>" class="btn btn-small btn-outline">Просмотр</a>
            <?php if ($user && ($user['role'] === 'admin' || $product['user_id'] == $user['id'])): ?>
            <div class="action-buttons">
                <a href="/products/<?php echo $product['id']; ?>/edit" class="btn btn-small btn-secondary" title="Редактировать">
                    ✏️
                </a>
                <button onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>')"
                        class="btn btn-small btn-danger" title="Удалить">
                    🗑️
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function deleteProduct(productId, productName) {
    if (confirm(`Вы уверены, что хотите удалить товар "${productName}"?\n\nЭто действие нельзя отменить.`)) {
        // Создаем форму для отправки POST запроса
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/products/${productId}/delete`;

        // Добавляем CSRF токен если есть
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }

        document.body.appendChild(form);
        form.submit();
    }
}

// Добавляем анимацию для карточек при загрузке
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.product-card');
    cards.forEach((card, index) => {
        // Задержка для staggered эффекта
        card.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>
