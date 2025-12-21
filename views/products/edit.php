<div class="page-header">
    <h1>Редактировать товар</h1>
    <a href="/products/<?php echo $product['id']; ?>" class="btn btn-secondary">← Назад к товару</a>
</div>

<form method="POST" action="/products/<?php echo $product['id']; ?>" enctype="multipart/form-data" class="product-form">
    <div class="form-section">
        <div class="form-row">
            <div class="form-group">
                <label for="name">Название товара *</label>
                <input type="text" id="name" name="name" required
                       class="form-input" value="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="form-group">
                <label for="price">Цена (₽) *</label>
                <input type="number" id="price" name="price" required
                       class="form-input" value="<?php echo $product['price']; ?>" min="0" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="category">Категория</label>
                <select id="category" name="category" class="form-input">
                    <option value="">Выберите категорию</option>
                    <option value="Электроника" <?php echo ($product['category'] ?? '') === 'Электроника' ? 'selected' : ''; ?>>Электроника</option>
                    <option value="Компьютеры" <?php echo ($product['category'] ?? '') === 'Компьютеры' ? 'selected' : ''; ?>>Компьютеры</option>
                    <option value="Офисная техника" <?php echo ($product['category'] ?? '') === 'Офисная техника' ? 'selected' : ''; ?>>Офисная техника</option>
                    <option value="Сетевое оборудование" <?php echo ($product['category'] ?? '') === 'Сетевое оборудование' ? 'selected' : ''; ?>>Сетевое оборудование</option>
                    <option value="Другое" <?php echo ($product['category'] ?? '') === 'Другое' ? 'selected' : ''; ?>>Другое</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Описание</label>
            <textarea id="description" name="description" rows="4"
                      class="form-input"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="image">Изображение товара</label>
            <input type="file" id="image" name="image" accept="image/*"
                   class="form-input">
            <small class="form-hint">Оставьте пустым, чтобы сохранить текущее изображение. Поддерживаемые форматы: JPG, PNG, GIF, WebP. Максимальный размер: 5MB</small>
            <?php if (!empty($product['image']) && $product['image'] !== '/css/placeholder-product.svg'): ?>
            <div class="current-image">
                <small>Текущее изображение:</small><br>
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Текущее изображение" style="max-width: 200px; max-height: 200px; margin-top: 0.5rem;">
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        <a href="/products/<?php echo $product['id']; ?>" class="btn btn-secondary">Отмена</a>
        <a href="/products/<?php echo $product['id']; ?>/delete" class="btn btn-danger"
           onclick="return confirm('Вы уверены, что хотите удалить этот товар?')">Удалить товар</a>
    </div>
</form>
