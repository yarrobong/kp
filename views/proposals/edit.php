<div class="page-header">
    <h1>Редактировать предложение</h1>
    <div class="page-actions">
        <a href="/proposals/<?php echo $proposal['id']; ?>" class="btn btn-secondary">Просмотр</a>
        <a href="/proposals" class="btn btn-secondary">← Назад к списку</a>
    </div>
</div>

<form method="POST" action="/proposals/<?php echo $proposal['id']; ?>" class="proposal-form">
    <!-- Информация о клиенте -->
    <div class="form-section">
        <h2>Информация о клиенте</h2>
        <div class="form-row">
            <div class="form-group">
                <label for="client_name">Имя клиента *</label>
                <input type="text" id="client_name" name="client_name" required
                       class="form-input" value="<?php echo htmlspecialchars($clientInfo['client_name']); ?>">
            </div>
            <div class="form-group">
                <label for="offer_date">Дата предложения *</label>
                <input type="date" id="offer_date" name="offer_date" required
                       class="form-input" value="<?php echo htmlspecialchars($proposal['offer_date']); ?>">
            </div>
        </div>
    </div>

    <!-- Выбор товаров -->
    <div class="form-section">
        <h2>Выбор товаров</h2>
        <div class="products-selection">
            <!-- Таблица товаров -->
            <div class="products-table-container">
                <table class="products-table" id="products-table">
                    <thead>
                        <tr class="table-header">
                            <th>Наименование товара</th>
                            <th>Цена</th>
                            <th>Количество</th>
                            <th>Сумма</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody id="product-rows">
                        <!-- Предзаполненные товары -->
                        <?php $rowCounter = 0; ?>
                        <?php foreach ($clientInfo['products'] as $product): ?>
                        <tr class="product-row" data-product-id="<?php echo $product['id']; ?>">
                            <td class="product-name-cell">
                                <div class="search-container">
                                    <input type="text" class="product-search-input form-input"
                                           placeholder="Начните вводить название товара..."
                                           value="<?php echo htmlspecialchars($product['name']); ?>"
                                           autocomplete="off">
                                    <div class="suggestions-dropdown" style="display: none;">
                                        <!-- Предложения товаров будут здесь -->
                                    </div>
                                    <input type="hidden" class="product-id-input" name="proposal_items[<?php echo ++$rowCounter; ?>][product_id]" value="<?php echo $product['id']; ?>">
                                </div>
                            </td>
                            <td class="product-price-cell">
                                <span class="price-display"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</span>
                            </td>
                            <td class="product-quantity-cell">
                                <input type="number" class="quantity-input form-input"
                                       name="proposal_items[<?php echo $rowCounter; ?>][quantity]"
                                       value="<?php echo $product['quantity']; ?>" min="1" max="999" required>
                            </td>
                            <td class="product-total-cell">
                                <span class="row-total"><?php echo number_format($product['total'], 0, ',', ' '); ?> ₽</span>
                            </td>
                            <td class="product-actions-cell">
                                <button type="button" class="btn btn-small btn-danger remove-product" title="Удалить товар">
                                    ✕
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions-inline">
                <button type="button" id="add-product-btn" class="btn btn-primary">➕ Добавить товар</button>
            </div>

            <div class="total-section">
                <div class="total-row">
                    <strong>Итого: <span id="total-amount"><?php echo number_format($proposal['total'], 0, ',', ' '); ?></span> ₽</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Кнопки действий -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        <a href="/proposals/<?php echo $proposal['id']; ?>" class="btn btn-secondary">Отмена</a>
        <a href="/proposals/<?php echo $proposal['id']; ?>/delete" class="btn btn-danger"
           onclick="return confirm('Вы уверены, что хотите удалить это предложение?')">Удалить предложение</a>
    </div>
</form>

<script>
// Управление товарами в предложении
class ProposalForm {
    constructor() {
        this.products = <?php echo json_encode($products); ?>;
        this.rowCounter = <?php echo $rowCounter; ?>;

        this.init();
    }

    init() {
        this.bindEvents();
        this.updateTotal();
    }

    bindEvents() {
        // Добавление новой строки товара
        document.getElementById('add-product-btn').addEventListener('click', () => {
            this.addProductRow();
        });

        // Удаление товара из списка
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-product')) {
                const row = e.target.closest('tr');
                this.removeProductRow(row);
            }
        });

        // Изменение количества
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                const row = e.target.closest('tr');
                this.updateRowTotal(row);
                this.updateTotal();
            }
        });

        // Поиск товаров в существующих строках
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('product-search-input')) {
                const row = e.target.closest('tr');
                this.handleProductSearch(e.target, row.querySelector('.suggestions-dropdown'));
            }
        });

        // Фокус на поле поиска
        document.addEventListener('focus', (e) => {
            if (e.target.classList.contains('product-search-input')) {
                const row = e.target.closest('tr');
                const suggestions = row.querySelector('.suggestions-dropdown');
                if (e.target.value.trim()) {
                    this.handleProductSearch(e.target, suggestions);
                }
            }
        }, true);

        // Клик вне поля поиска
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-container')) {
                document.querySelectorAll('.suggestions-dropdown').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });
    }

    addProductRow(selectedProduct = null) {
        const container = document.getElementById('product-rows');
        const row = document.createElement('tr');
        row.className = 'product-row';
        row.dataset.rowId = ++this.rowCounter;

        const productData = selectedProduct || { id: '', name: '', price: 0 };
        const quantity = selectedProduct ? 1 : '';
        const total = selectedProduct ? productData.price : 0;

        row.innerHTML = `
            <td class="product-name-cell">
                <div class="search-container">
                    <input type="text" class="product-search-input form-input"
                           placeholder="Начните вводить название товара..."
                           value="${productData.name}"
                           autocomplete="off">
                    <div class="suggestions-dropdown" style="display: none;">
                        <!-- Предложения товаров будут здесь -->
                    </div>
                    <input type="hidden" class="product-id-input" name="proposal_items[${this.rowCounter}][product_id]" value="${productData.id}">
                </div>
            </td>
            <td class="product-price-cell">
                <span class="price-display">${this.formatPrice(productData.price)}</span>
            </td>
            <td class="product-quantity-cell">
                <input type="number" class="quantity-input form-input"
                       name="proposal_items[${this.rowCounter}][quantity]"
                       value="${quantity}" min="1" max="999" ${selectedProduct ? 'required' : ''}>
            </td>
            <td class="product-total-cell">
                <span class="row-total">${this.formatPrice(total)}</span>
            </td>
            <td class="product-actions-cell">
                <button type="button" class="btn btn-small btn-danger remove-product" title="Удалить товар">
                    ✕
                </button>
            </td>
        `;

        container.appendChild(row);

        // Привязываем события для новой строки
        this.bindRowEvents(row);

        if (selectedProduct) {
            this.updateTotal();
        }
    }

    bindRowEvents(row) {
        const searchInput = row.querySelector('.product-search-input');
        const suggestions = row.querySelector('.suggestions-dropdown');

        // Поиск товаров
        searchInput.addEventListener('input', (e) => {
            this.handleProductSearch(e.target, suggestions);
        });

        // Фокус на поле поиска
        searchInput.addEventListener('focus', () => {
            if (searchInput.value.trim()) {
                this.handleProductSearch(searchInput, suggestions);
            }
        });

        // Клавиатурная навигация
        searchInput.addEventListener('keydown', (e) => {
            this.handleKeyboardNavigation(e, suggestions, row);
        });
    }

    handleProductSearch(input, suggestions) {
        const query = input.value.toLowerCase().trim();
        suggestions.innerHTML = '';

        if (query.length < 1) {
            suggestions.style.display = 'none';
            return;
        }

        const matches = this.products.filter(product =>
            product.name.toLowerCase().includes(query)
        ).slice(0, 10);

        if (matches.length > 0) {
            matches.forEach((product, index) => {
                const item = document.createElement('div');
                item.className = 'suggestion-item';
                item.dataset.productId = product.id;
                item.dataset.productName = product.name;
                item.dataset.productPrice = product.price;
                item.innerHTML = `
                    <div class="suggestion-name">${this.highlightMatch(product.name, query)}</div>
                    <div class="suggestion-price">${this.formatPrice(product.price)}</div>
                `;

                item.addEventListener('click', () => {
                    this.selectProductFromSearch(product, input.closest('tr'));
                });

                suggestions.appendChild(item);
            });
            suggestions.style.display = 'block';
        } else {
            suggestions.style.display = 'none';
        }
    }

    highlightMatch(text, query) {
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    selectProductFromSearch(product, row) {
        const searchInput = row.querySelector('.product-search-input');
        const idInput = row.querySelector('.product-id-input');
        const priceDisplay = row.querySelector('.price-display');
        const quantityInput = row.querySelector('.quantity-input');
        const suggestions = row.querySelector('.suggestions-dropdown');

        // Заполняем поля
        searchInput.value = product.name;
        idInput.value = product.id;
        priceDisplay.textContent = this.formatPrice(product.price);

        // Устанавливаем количество по умолчанию
        if (!quantityInput.value || quantityInput.value === '0') {
            quantityInput.value = '1';
        }

        // Скрываем предложения
        suggestions.style.display = 'none';

        // Пересчитываем сумму
        this.updateRowTotal(row);
        this.updateTotal();

        // Добавляем required к количеству
        quantityInput.required = true;
    }

    handleKeyboardNavigation(e, suggestions, row) {
        const items = suggestions.querySelectorAll('.suggestion-item');
        const visibleItems = Array.from(items).filter(item => item.style.display !== 'none');

        if (visibleItems.length === 0) return;

        let activeIndex = -1;
        visibleItems.forEach((item, index) => {
            if (item.classList.contains('active')) {
                activeIndex = index;
                item.classList.remove('active');
            }
        });

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, visibleItems.length - 1);
                visibleItems[activeIndex].classList.add('active');
                break;
            case 'ArrowUp':
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                visibleItems[activeIndex].classList.add('active');
                break;
            case 'Enter':
                e.preventDefault();
                if (activeIndex >= 0) {
                    const product = {
                        id: visibleItems[activeIndex].dataset.productId,
                        name: visibleItems[activeIndex].dataset.productName,
                        price: parseFloat(visibleItems[activeIndex].dataset.productPrice)
                    };
                    this.selectProductFromSearch(product, row);
                }
                break;
            case 'Escape':
                suggestions.style.display = 'none';
                break;
        }
    }

    updateRowTotal(row) {
        const quantityInput = row.querySelector('.quantity-input');
        const priceText = row.querySelector('.price-display').textContent;
        const totalDisplay = row.querySelector('.row-total');

        const quantity = parseInt(quantityInput.value) || 0;
        const price = this.parsePrice(priceText) || 0;
        const total = price * quantity;

        totalDisplay.textContent = this.formatPrice(total);
    }

    removeProductRow(row) {
        // Не удаляем если это последняя строка
        const rows = document.querySelectorAll('.product-row');
        if (rows.length > 1) {
            row.remove();
            this.updateTotal();
        } else {
            // Очищаем строку вместо удаления
            this.clearProductRow(row);
        }
    }

    clearProductRow(row) {
        const searchInput = row.querySelector('.product-search-input');
        const idInput = row.querySelector('.product-id-input');
        const priceDisplay = row.querySelector('.price-display');
        const quantityInput = row.querySelector('.quantity-input');
        const totalDisplay = row.querySelector('.row-total');

        searchInput.value = '';
        idInput.value = '';
        priceDisplay.textContent = this.formatPrice(0);
        quantityInput.value = '';
        totalDisplay.textContent = this.formatPrice(0);
        quantityInput.required = false;

        this.updateTotal();
    }

    updateTotal() {
        let total = 0;
        const rows = document.querySelectorAll('.product-row');

        rows.forEach(row => {
            const totalText = row.querySelector('.row-total').textContent;
            const amount = this.parsePrice(totalText) || 0;
            total += amount;
        });

        document.getElementById('total-amount').textContent = this.formatPrice(total);
    }

    parsePrice(priceText) {
        return parseFloat(priceText.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
    }

    formatPrice(price) {
        return new Intl.NumberFormat('ru-RU').format(price) + ' ₽';
    }
}

// Инициализация формы
document.addEventListener('DOMContentLoaded', () => {
    new ProposalForm();
});
</script>
