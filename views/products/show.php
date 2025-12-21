<div class="page-header">
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
    <div class="page-actions">
        <a href="/products/<?php echo $product['id']; ?>/edit" class="btn btn-secondary">Редактировать</a>
        <a href="/products" class="btn btn-secondary">← Назад к списку</a>
    </div>
</div>

<div class="product-detail">
    <div class="product-image-large">
        <img src="<?php echo htmlspecialchars($product['image']); ?>"
             alt="<?php echo htmlspecialchars($product['name']); ?>">
    </div>

    <div class="product-info-detail">
        <div class="product-price-large">
            <?php echo number_format($product['price'], 0, ',', ' '); ?> ₽
        </div>

        <?php if (!empty($product['category'])): ?>
        <div class="product-category-badge">
            <?php echo htmlspecialchars($product['category']); ?>
        </div>
        <?php endif; ?>

        <div class="product-description-full">
            <h3>Описание</h3>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        </div>

        <div class="product-meta">
            <div class="meta-item">
                <span class="meta-label">ID товара:</span>
                <span class="meta-value"><?php echo $product['id']; ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Дата создания:</span>
                <span class="meta-value"><?php echo date('d.m.Y H:i', strtotime($product['created_at'])); ?></span>
            </div>
            <?php if (isset($product['updated_at'])): ?>
            <div class="meta-item">
                <span class="meta-label">Последнее обновление:</span>
                <span class="meta-value"><?php echo date('d.m.Y H:i', strtotime($product['updated_at'])); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
