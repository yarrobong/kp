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
            <div class="add-product-row">
                <button type="button" id="add-product-btn" class="btn btn-secondary">Добавить товар</button>
            </div>

            <div id="products-table" class="products-table">
                <div class="table-header">
                    <div>Наименование товара</div>
                    <div>Цена</div>
                    <div>Количество</div>
                    <div>Сумма</div>
                    <div>Действия</div>
                </div>

                <!-- Предзаполненные товары -->
                <?php $rowCounter = 0; ?>
                <?php foreach ($clientInfo['products'] as $product): ?>
                <div class="product-row" data-product-id="<?php echo $product['id']; ?>">
                    <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                    <div class="product-price"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</div>
                    <div class="product-quantity">
                        <input type="number" class="quantity-input" value="<?php echo $product['quantity']; ?>" min="1" max="999">
                        <input type="hidden" name="proposal_items[<?php echo $product['id']; ?>][product_id]" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="proposal_items[<?php echo $product['id']; ?>][quantity]" value="<?php echo $product['quantity']; ?>">
                    </div>
                    <div class="product-total"><?php echo number_format($product['total'], 0, ',', ' '); ?> ₽</div>
                    <div class="product-actions">
                        <button type="button" class="btn btn-small btn-danger remove-product">Удалить</button>
                    </div>
                </div>
                <?php $rowCounter++; ?>
                <?php endforeach; ?>
            </div>

            <div class="total-section">
                <strong>Итого: <span id="total-amount"><?php echo number_format($proposal['total'], 0, ',', ' '); ?></span> ₽</strong>
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

<!-- Модальное окно для выбора товара -->
<div id="product-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Выберите товар</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="product-search">
                <input type="text" id="product-search" placeholder="Поиск товаров..."
                       class="form-input">
            </div>
            <div id="product-list" class="product-list">
                <?php foreach ($products as $product): ?>
                <div class="product-item" data-product-id="<?php echo $product['id']; ?>"
                     data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                     data-product-price="<?php echo $product['price']; ?>">
                    <div class="product-info">
                        <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                        <div class="product-meta">
                            <?php echo htmlspecialchars($product['description']); ?>
                            <span class="price"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-small select-product">Выбрать</button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Управление товарами в предложении
class ProposalForm {
    constructor() {
        this.products = <?php echo json_encode($products); ?>;
        this.selectedProducts = new Map();
        this.rowCounter = <?php echo $rowCounter; ?>;

        // Инициализируем существующие товары
        this.initExistingProducts();

        this.init();
    }

    initExistingProducts() {
        const rows = document.querySelectorAll('.product-row');
        rows.forEach(row => {
            const productId = row.dataset.productId;
            const product = this.products.find(p => p.id == productId);
            if (product) {
                const quantityInput = row.querySelector('.quantity-input');
                const quantity = parseInt(quantityInput.value) || 1;

                this.selectedProducts.set(productId, {
                    ...product,
                    quantity: quantity,
                    rowId: ++this.rowCounter
                });
            }
        });
    }

    init() {
        this.bindEvents();
        this.updateTotal();
    }

    bindEvents() {
        // Добавление товара
        document.getElementById('add-product-btn').addEventListener('click', () => {
            this.showProductModal();
        });

        // Модальное окно
        document.querySelector('.modal-close').addEventListener('click', () => {
            this.hideProductModal();
        });

        document.getElementById('product-modal').addEventListener('click', (e) => {
            if (e.target === e.currentTarget) {
                this.hideProductModal();
            }
        });

        // Поиск товаров
        document.getElementById('product-search').addEventListener('input', (e) => {
            this.searchProducts(e.target.value);
        });

        // Выбор товара
        document.querySelectorAll('.select-product').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const productItem = e.target.closest('.product-item');
                const productId = productItem.dataset.productId;
                this.selectProduct(productId);
            });
        });

        // Удаление товара из списка
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-product')) {
                const row = e.target.closest('.product-row');
                const productId = row.dataset.productId;
                this.removeProduct(productId);
            }
        });

        // Изменение количества
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                const row = e.target.closest('.product-row');
                const productId = row.dataset.productId;
                const quantity = parseInt(e.target.value) || 0;
                this.updateQuantity(productId, quantity);
            }
        });
    }

    showProductModal() {
        document.getElementById('product-modal').style.display = 'block';
    }

    hideProductModal() {
        document.getElementById('product-modal').style.display = 'none';
    }

    searchProducts(query) {
        const items = document.querySelectorAll('.product-item');
        const lowerQuery = query.toLowerCase();

        items.forEach(item => {
            const name = item.dataset.productName.toLowerCase();
            const visible = name.includes(lowerQuery);
            item.style.display = visible ? 'flex' : 'none';
        });
    }

    selectProduct(productId) {
        if (this.selectedProducts.has(productId)) {
            alert('Этот товар уже добавлен');
            return;
        }

        const product = this.products.find(p => p.id == productId);
        if (!product) return;

        this.selectedProducts.set(productId, {
            ...product,
            quantity: 1,
            rowId: ++this.rowCounter
        });

        this.addProductRow(product);
        this.updateTotal();
        this.hideProductModal();
    }

    addProductRow(product) {
        const table = document.getElementById('products-table');
        const row = document.createElement('div');
        row.className = 'product-row';
        row.dataset.productId = product.id;

        row.innerHTML = `
            <div class="product-name">${product.name}</div>
            <div class="product-price">${this.formatPrice(product.price)}</div>
            <div class="product-quantity">
                <input type="number" class="quantity-input" value="1" min="1" max="999">
                <input type="hidden" name="proposal_items[${product.id}][product_id]" value="${product.id}">
            </div>
            <div class="product-total">${this.formatPrice(product.price)}</div>
            <div class="product-actions">
                <button type="button" class="btn btn-small btn-danger remove-product">Удалить</button>
            </div>
        `;

        table.appendChild(row);
    }

    removeProduct(productId) {
        const row = document.querySelector(`.product-row[data-product-id="${productId}"]`);
        if (row) {
            row.remove();
        }
        this.selectedProducts.delete(productId);
        this.updateTotal();
    }

    updateQuantity(productId, quantity) {
        if (this.selectedProducts.has(productId)) {
            const product = this.selectedProducts.get(productId);
            product.quantity = quantity;

            const row = document.querySelector(`.product-row[data-product-id="${productId}"]`);
            if (row) {
                const totalCell = row.querySelector('.product-total');
                const quantityInput = row.querySelector('.quantity-input');

                if (quantityInput) {
                    quantityInput.value = quantity;
                }

                // Обновляем hidden input для количества
                let quantityHidden = row.querySelector('input[name*="quantity"]');
                if (!quantityHidden) {
                    quantityHidden = document.createElement('input');
                    quantityHidden.type = 'hidden';
                    quantityHidden.name = `proposal_items[${productId}][quantity]`;
                    row.querySelector('.product-quantity').appendChild(quantityHidden);
                }
                quantityHidden.value = quantity;

                if (totalCell) {
                    totalCell.textContent = this.formatPrice(product.price * quantity);
                }
            }
        }
        this.updateTotal();
    }

    updateTotal() {
        let total = 0;
        this.selectedProducts.forEach(product => {
            total += product.price * product.quantity;
        });

        document.getElementById('total-amount').textContent = this.formatPrice(total);
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
