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
                <button type="button" id="add-product-btn" class="btn btn-secondary">Добавить еще товар</button>
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
        this.addInitialProductRow(); // Добавляем первую строку товара
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
                const row = e.target.closest('.product-row');
                this.removeProductRow(row);
            }
        });

        // Изменение товара в выпадающем списке
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('product-select')) {
                this.updateProductInfo(e.target);
            }
        });

        // Изменение количества
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                this.updateRowTotal(e.target.closest('.product-row'));
                this.updateTotal();
            }
        });
    }

    addInitialProductRow() {
        this.addProductRow();
    }

    addProductRow() {
        const container = document.getElementById('product-rows');
        const row = document.createElement('div');
        row.className = 'product-row';
        row.dataset.rowId = ++this.rowCounter;

        // Создаем опции для выпадающего списка
        const options = this.products.map(product =>
            `<option value="${product.id}" data-price="${product.price}" data-name="${product.name.replace(/"/g, '&quot;')}">
                ${product.name} - ${this.formatPrice(product.price)}
            </option>`
        ).join('');

        row.innerHTML = `
            <div class="row-fields">
                <div class="field-group">
                    <label>Товар</label>
                    <select class="product-select form-input" name="proposal_items[${this.rowCounter}][product_id]" required>
                        <option value="">Выберите товар...</option>
                        ${options}
                    </select>
                </div>
                <div class="field-group">
                    <label>Количество</label>
                    <input type="number" class="quantity-input form-input" name="proposal_items[${this.rowCounter}][quantity]"
                           value="1" min="1" max="999" required>
                </div>
                <div class="field-group">
                    <label>Цена за шт.</label>
                    <div class="price-display">0 ₽</div>
                </div>
                <div class="field-group">
                    <label>Сумма</label>
                    <div class="row-total">0 ₽</div>
                </div>
                <div class="field-group actions">
                    <button type="button" class="btn btn-small btn-danger remove-product" title="Удалить товар">
                        🗑️
                    </button>
                </div>
            </div>
        `;

        container.appendChild(row);
        this.updateTotal();
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

    formatPrice(price) {
        return new Intl.NumberFormat('ru-RU').format(price) + ' ₽';
    }
}

// Инициализация формы
document.addEventListener('DOMContentLoaded', () => {
    new ProposalForm();
});
</script>
