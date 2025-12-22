<div class="page-header">
    <h1>Создать коммерческое предложение</h1>
    <a href="/proposals" class="btn btn-secondary">← Назад к списку</a>
</div>

<form method="POST" action="/proposals" class="proposal-form">
    <!-- Информация о клиенте -->
    <div class="form-section">
        <h2>Информация о клиенте</h2>
        <div class="form-row">
            <div class="form-group">
                <label for="client_name">Имя клиента *</label>
                <input type="text" id="client_name" name="client_name" required
                       class="form-input" placeholder="Введите имя клиента">
            </div>
            <div class="form-group">
                <label for="offer_date">Дата предложения *</label>
                <input type="date" id="offer_date" name="offer_date" required
                       class="form-input" value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>
    </div>

    <!-- Выбор товаров -->
    <div class="form-section">
        <h2>Выбор товаров</h2>
        <div class="products-selection">
            <div id="product-rows" class="product-rows">
                <!-- Строки товаров будут добавляться сюда -->
            </div>

            <div class="form-actions-inline">
                <button type="button" id="add-product-btn" class="btn btn-primary">➕ Добавить товар</button>
            </div>

            <div class="total-section">
                <strong>Итого: <span id="total-amount">0</span> ₽</strong>
            </div>
        </div>
    </div>

    <!-- Кнопки действий -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Создать предложение</button>
        <a href="/proposals" class="btn btn-secondary">Отмена</a>
    </div>
</form>


<script>
// Управление товарами в предложении
class ProposalForm {
    constructor() {
        this.products = <?php echo json_encode($products); ?>;
        this.rowCounter = 0;

        this.init();
    }

    init() {
        this.bindEvents();
        this.addProductRow(); // Добавляем первую пустую строку товара
    }

    bindEvents() {
        // Добавление новой строки товара
        document.getElementById('add-product-btn').addEventListener('click', () => {
            this.addProductRow();
        });
    }


    addProductRow(selectedProduct = null) {
        const container = document.getElementById('product-rows');
        const row = document.createElement('div');
        row.className = 'product-row';
        row.dataset.rowId = ++this.rowCounter;

        const productData = selectedProduct || { id: '', name: '', price: 0 };
        const quantity = selectedProduct ? 1 : '';
        const total = selectedProduct ? productData.price : 0;

        row.innerHTML = `
            <div class="product-search-row">
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
                <div class="quantity-container">
                    <input type="number" class="quantity-input form-input" placeholder="Кол-во"
                           name="proposal_items[${this.rowCounter}][quantity]"
                           value="${quantity}" min="1" max="999" ${selectedProduct ? 'required' : ''}>
                </div>
                <div class="price-container">
                    <span class="price-display">${this.formatPrice(productData.price)}</span>
                </div>
                <div class="total-container">
                    <span class="row-total">${this.formatPrice(total)}</span>
                </div>
                <div class="actions-container">
                    <button type="button" class="btn btn-small btn-danger remove-product" title="Удалить товар">
                        ✕
                    </button>
                </div>
            </div>
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
        const quantityInput = row.querySelector('.quantity-input');
        const removeBtn = row.querySelector('.remove-product');

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

        // Клик вне поля поиска
        document.addEventListener('click', (e) => {
            if (!row.contains(e.target)) {
                suggestions.style.display = 'none';
            }
        });

        // Изменение количества
        quantityInput.addEventListener('input', () => {
            this.updateRowTotal(row);
            this.updateTotal();
        });

        // Удаление строки
        removeBtn.addEventListener('click', () => {
            this.removeProductRow(row);
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
        ).slice(0, 5); // Ограничим до 5 результатов

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
                    this.selectProductFromSearch(product, input.closest('.product-row'));
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
        const price = parseFloat(priceText.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
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
            alert('Должен остаться хотя бы один товар');
        }
    }

    updateProductInfo(selectElement) {
        const row = selectElement.closest('.product-row');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const quantity = parseInt(row.querySelector('.quantity-input').value) || 1;

        // Обновляем отображение цены
        row.querySelector('.price-display').textContent = this.formatPrice(price);

        // Обновляем сумму строки
        this.updateRowTotal(row);
        this.updateTotal();
    }

    updateRowTotal(row) {
        const select = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const totalDisplay = row.querySelector('.row-total');

        if (select && quantityInput && totalDisplay) {
            const selectedOption = select.options[select.selectedIndex];
            const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const quantity = parseInt(quantityInput.value) || 0;
            const total = price * quantity;

            totalDisplay.textContent = this.formatPrice(total);
        }
    }

    updateTotal() {
        let total = 0;
        const rows = document.querySelectorAll('.product-row');

        rows.forEach(row => {
            const totalText = row.querySelector('.row-total').textContent;
            const amount = parseFloat(totalText.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
            total += amount;
        });

        document.getElementById('total-amount').textContent = this.formatPrice(total);
    }

    updateProductInfo(selectElement) {
        // Этот метод больше не нужен для нового интерфейса
        // Оставлен для совместимости
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
