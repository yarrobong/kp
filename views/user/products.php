<div class="page-header">
    <h1>📦 Мои товары</h1>
    <div class="header-actions">
        <a href="/products/create" class="btn btn-primary">➕ Добавить товар</a>
        <a href="/user" class="btn btn-secondary">← Личный кабинет</a>
    </div>
</div>

<?php if (empty($products)): ?>
    <div class="empty-state">
        <div class="empty-state-icon">📦</div>
        <h2>У вас пока нет товаров</h2>
        <p>Начните с добавления первого товара в ваш каталог</p>
        <a href="/products/create" class="btn btn-primary">Добавить первый товар</a>
    </div>
<?php else: ?>
    <!-- Статистика товаров -->
    <div class="stats-bar">
        <div class="stat-item">
            <span class="stat-value"><?php echo count($products); ?></span>
            <span class="stat-label">Всего товаров</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?php echo count(array_filter($products, fn($p) => !empty($p['image']))); ?></span>
            <span class="stat-label">С изображениями</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">
                <?php
                $totalPrice = array_sum(array_column($products, 'price'));
                echo number_format($totalPrice, 0, ',', ' ');
                ?> ₽
            </span>
            <span class="stat-label">Общая стоимость</span>
        </div>
    </div>

    <!-- Фильтры и поиск -->
    <div class="filters-section">
        <div class="filters-row">
            <div class="search-box">
                <input type="text" id="productSearch" placeholder="🔍 Поиск товаров..." class="form-input">
            </div>
            <div class="sort-select">
                <select id="productSort" class="form-input">
                    <option value="name">По названию</option>
                    <option value="price-asc">По цене (возр.)</option>
                    <option value="price-desc">По цене (убыв.)</option>
                    <option value="date-desc">Сначала новые</option>
                    <option value="date-asc">Сначала старые</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Список товаров -->
    <div class="products-grid" id="productsGrid">
        <?php foreach ($products as $product): ?>
        <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
            <div class="product-image">
                <?php if (!empty($product['image'])): ?>
                    <img src="<?php echo htmlspecialchars($product['image']); ?>"
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    <div class="placeholder-image">
                        <?php echo file_get_contents(__DIR__ . '/../../css/placeholder-product.svg'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="product-info">
                <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="product-description">
                    <?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>
                    <?php if (strlen($product['description']) > 100): ?>...<?php endif; ?>
                </p>
                <div class="product-meta">
                    <span class="product-price"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</span>
                    <?php if (!empty($product['category'])): ?>
                    <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="product-dates">
                    <small>Создан: <?php echo date('d.m.Y', strtotime($product['created_at'])); ?></small>
                    <?php if ($product['updated_at'] !== $product['created_at']): ?>
                    <small>Обновлен: <?php echo date('d.m.Y', strtotime($product['updated_at'])); ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="product-actions">
                <a href="/products/<?php echo $product['id']; ?>" class="btn btn-small">👁 Просмотр</a>
                <a href="/products/<?php echo $product['id']; ?>/edit" class="btn btn-small btn-secondary">✏ Редактировать</a>
                <button onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>')"
                        class="btn btn-small btn-danger">🗑 Удалить</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="deleteModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Подтверждение удаления</h3>
                <span class="modal-close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>Вы действительно хотите удалить товар "<span id="deleteProductName"></span>"?</p>
                <p class="warning-text">Это действие нельзя отменить.</p>
            </div>
            <div class="modal-footer">
                <button onclick="closeDeleteModal()" class="btn btn-secondary">Отмена</button>
                <button id="confirmDeleteBtn" onclick="confirmDelete()" class="btn btn-danger">Удалить</button>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
// Поиск товаров
document.getElementById('productSearch').addEventListener('input', function() {
    filterProducts();
});

// Сортировка товаров
document.getElementById('productSort').addEventListener('change', function() {
    sortProducts();
});

function filterProducts() {
    const searchTerm = document.getElementById('productSearch').value.toLowerCase();
    const cards = document.querySelectorAll('.product-card');

    cards.forEach(card => {
        const title = card.querySelector('.product-title').textContent.toLowerCase();
        const description = card.querySelector('.product-description').textContent.toLowerCase();

        if (title.includes(searchTerm) || description.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function sortProducts() {
    const sortBy = document.getElementById('productSort').value;
    const grid = document.getElementById('productsGrid');
    const cards = Array.from(grid.children);

    cards.sort((a, b) => {
        switch (sortBy) {
            case 'name':
                return a.querySelector('.product-title').textContent.localeCompare(
                    b.querySelector('.product-title').textContent);

            case 'price-asc':
                return parseFloat(a.querySelector('.product-price').textContent.replace(/\s|₽/g, '')) -
                       parseFloat(b.querySelector('.product-price').textContent.replace(/\s|₽/g, ''));

            case 'price-desc':
                return parseFloat(b.querySelector('.product-price').textContent.replace(/\s|₽/g, '')) -
                       parseFloat(a.querySelector('.product-price').textContent.replace(/\s|₽/g, ''));

            case 'date-desc':
                return new Date(b.dataset.createdAt || 0) - new Date(a.dataset.createdAt || 0);

            case 'date-asc':
                return new Date(a.dataset.createdAt || 0) - new Date(b.dataset.createdAt || 0);

            default:
                return 0;
        }
    });

    cards.forEach(card => grid.appendChild(card));
}

// Модальное окно удаления
let productToDelete = null;

function deleteProduct(id, name) {
    productToDelete = id;
    document.getElementById('deleteProductName').textContent = name;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    productToDelete = null;
}

function confirmDelete() {
    if (!productToDelete) return;

    fetch(`/products/${productToDelete}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Удаляем карточку товара
            const card = document.querySelector(`[data-product-id="${productToDelete}"]`);
            if (card) {
                card.remove();
            }

            // Обновляем счетчики
            updateStats();

            closeDeleteModal();
            showMessage('Товар успешно удален', 'success');
        } else {
            showMessage(data.message || 'Ошибка при удалении товара', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Ошибка при удалении товара', 'error');
    });
}

function updateStats() {
    const cards = document.querySelectorAll('.product-card:not([style*="display: none"])');
    const totalCount = cards.length;
    const withImagesCount = Array.from(cards).filter(card =>
        card.querySelector('img') && !card.querySelector('img').src.includes('placeholder')
    ).length;

    // Обновляем статистику
    document.querySelector('.stat-value').textContent = totalCount;
    document.querySelectorAll('.stat-value')[1].textContent = withImagesCount;
}

function showMessage(message, type) {
    // Создаем элемент уведомления
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">×</button>
    `;

    document.body.appendChild(notification);

    // Автоматически скрываем через 5 секунд
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Закрытие модального окна по клику вне его
window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        closeDeleteModal();
    }
}

// Добавляем даты создания для сортировки
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.product-card').forEach(card => {
        const dateText = card.querySelector('.product-dates small').textContent;
        const dateMatch = dateText.match(/Создан: (\d{2}\.\d{2}\.\d{4})/);
        if (dateMatch) {
            card.dataset.createdAt = dateMatch[1].split('.').reverse().join('-');
        }
    });
});
</script>
