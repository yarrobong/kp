<!-- Hero секция товаров -->
<div class="products-hero">
    <h1>🛍️ Каталог товаров</h1>
    <p>Откройте для себя наш премиум каталог товаров с передовыми технологиями и инновационными решениями</p>
    <?php if ($user): ?>
    <a href="/products/create" class="btn btn-primary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Добавить товар
    </a>
    <?php endif; ?>
</div>

<!-- Поиск и фильтры -->
<div class="filters-section">
    <form method="GET" class="search-form">
        <div class="search-input">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                   placeholder="🔍 Искать товары..." class="form-input">
            <button type="submit" class="btn btn-secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 21L16.5 16.5M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Поиск
            </button>
        </div>
    </form>

    <?php if ($search || $category): ?>
    <div class="active-filters">
        <span>🎯 Активные фильтры:</span>
        <?php if ($search): ?>
        <span class="filter-tag" style="--tag-index: 0;">Поиск: "<?php echo htmlspecialchars($search); ?>"</span>
        <?php endif; ?>
        <a href="/products" class="btn btn-small">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Сбросить
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Список товаров -->
<div class="products-grid">
    <?php if (empty($products)): ?>
    <div class="empty-state">
        <h3><?php echo $search ? '🤔 Товары не найдены' : '📦 Каталог пуст'; ?></h3>
        <p><?php echo $search ? 'Попробуйте изменить поисковый запрос или сбросьте фильтры' : 'Добавьте первый товар в каталог и начните продавать!'; ?></p>
        <a href="/products/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?php echo $search ? 'Добавить товар' : 'Добавить первый товар'; ?>
        </a>
    </div>
    <?php else: ?>
    <?php foreach ($products as $product): ?>
    <div class="product-card">
        <div class="product-image-container">
            <?php if (!empty($product['image']) && $product['image'] !== '/css/placeholder-product.svg'): ?>
                <div class="product-image">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>"
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
            <?php else: ?>
                <div class="product-placeholder">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="1.5" opacity="0.5"/>
                        <path d="M7 10L9 12L13 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.5"/>
                        <circle cx="16" cy="10" r="2" stroke="currentColor" stroke-width="1.5" opacity="0.5"/>
                    </svg>
                </div>
            <?php endif; ?>
        </div>

        <div class="product-content">
            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>

            <div class="product-description">
                <?php
                $description = htmlspecialchars($product['description'] ?? '');
                echo strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description;
                ?>
            </div>

            <div class="product-price"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</div>

            <div class="product-meta">
                <?php if (!empty($product['category'])): ?>
                <span>📁 <?php echo htmlspecialchars($product['category']); ?></span>
                <?php endif; ?>
                <span>🕒 <?php echo date('d.m.Y', strtotime($product['created_at'])); ?></span>
            </div>

            <div class="product-actions">
                <a href="/products/<?php echo $product['id']; ?>" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M2.4578 12C3.73207 7.94281 7.52236 5 12 5C16.4776 5 20.2679 7.94281 21.5422 12C20.2679 16.0572 16.4776 19 12 19C7.52236 19 3.73207 16.0572 2.4578 12Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Просмотр
                </a>
                <?php if ($user && ($user['role'] === 'admin' || $product['user_id'] == $user['id'])): ?>
                <a href="/products/<?php echo $product['id']; ?>/edit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18.5 2.5C18.8978 2.10217 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.10217 21.5 2.5C21.8978 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8978 5.10217 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Редактировать
                </a>
                <button onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>')"
                        class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 7L18.1327 19.1425C18.0579 20.1891 17.187 21 16.1378 21H7.86224C6.81296 21 5.94208 20.1891 5.86732 19.1425L5 7M10 11V17M14 11V17M15 7V4C15 3.44772 14.5523 3 14 3H10C9.44772 3 9 3.44772 9 4V7M4 7H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Удалить
                </button>
                <?php endif; ?>
            </div>
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
