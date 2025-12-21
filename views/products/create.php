<div class="page-header">
    <h1>Добавить товар</h1>
    <a href="/products" class="btn btn-secondary">← Назад к списку</a>
</div>

<form method="POST" action="/products" class="product-form">
    <div class="form-section">
        <div class="form-row">
            <div class="form-group">
                <label for="name">Название товара *</label>
                <input type="text" id="name" name="name" required
                       class="form-input" placeholder="Введите название товара">
            </div>
            <div class="form-group">
                <label for="price">Цена (₽) *</label>
                <input type="number" id="price" name="price" required
                       class="form-input" placeholder="0" min="0" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="category">Категория</label>
                <select id="category" name="category" class="form-input">
                    <option value="">Выберите категорию</option>
                    <option value="Электроника">Электроника</option>
                    <option value="Компьютеры">Компьютеры</option>
                    <option value="Офисная техника">Офисная техника</option>
                    <option value="Сетевое оборудование">Сетевое оборудование</option>
                    <option value="Другое">Другое</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Описание</label>
            <textarea id="description" name="description" rows="4"
                      class="form-input" placeholder="Подробное описание товара"></textarea>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Добавить товар</button>
        <a href="/products" class="btn btn-secondary">Отмена</a>
    </div>
</form>
