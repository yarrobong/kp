<div class="page-header">
    <h1><?php echo htmlspecialchars($proposal['title']); ?></h1>
    <div class="page-actions">
        <a href="/proposals/<?php echo $proposal['id']; ?>/edit" class="btn btn-secondary">Редактировать</a>
        <a href="/proposals/<?php echo $proposal['id']; ?>/pdf" class="btn btn-success">Скачать PDF</a>
        <a href="/proposals" class="btn btn-secondary">← Назад к списку</a>
    </div>
</div>

<div class="proposal-detail">
    <!-- Информация о предложении -->
    <div class="proposal-info-section">
        <h2>Информация о предложении</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Номер предложения:</span>
                <span class="info-value"><?php echo htmlspecialchars($proposal['offer_number']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Дата создания:</span>
                <span class="info-value"><?php echo htmlspecialchars($proposal['offer_date']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Статус:</span>
                <span class="status-badge status-<?php echo htmlspecialchars($proposal['status']); ?>">
                    <?php
                    $statusLabels = [
                        'draft' => 'Черновик',
                        'sent' => 'Отправлено',
                        'accepted' => 'Принято',
                        'rejected' => 'Отклонено'
                    ];
                    echo $statusLabels[$proposal['status']] ?? $proposal['status'];
                    ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Общая сумма:</span>
                <span class="info-value total-amount"><?php echo number_format($proposal['total'], 0, ',', ' '); ?> ₽</span>
            </div>
        </div>
    </div>

    <!-- Информация о клиенте -->
    <div class="client-info-section">
        <h2>Информация о клиенте</h2>
        <div class="client-info">
            <div class="client-name">
                <span class="client-label">Клиент:</span>
                <span class="client-value"><?php echo htmlspecialchars($clientInfo['client_name']); ?></span>
            </div>
        </div>
    </div>

    <!-- Состав предложения -->
    <div class="products-section">
        <h2>Состав предложения</h2>
        <div class="products-table">
            <div class="table-header">
                <div>Наименование товара</div>
                <div>Описание</div>
                <div>Цена за единицу</div>
                <div>Количество</div>
                <div>Сумма</div>
            </div>

            <?php foreach ($clientInfo['products'] as $product): ?>
            <div class="table-row">
                <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                <div class="product-description"><?php echo htmlspecialchars($product['description']); ?></div>
                <div class="product-price"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</div>
                <div class="product-quantity"><?php echo $product['quantity']; ?> шт.</div>
                <div class="product-total"><?php echo number_format($product['total'], 0, ',', ' '); ?> ₽</div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="proposal-total-section">
            <div class="total-row">
                <span class="total-label">Итого:</span>
                <span class="total-amount"><?php echo number_format($proposal['total'], 0, ',', ' '); ?> ₽</span>
            </div>
        </div>
    </div>

    <!-- Дополнительная информация -->
    <div class="proposal-meta-section">
        <h3>Дополнительная информация</h3>
        <div class="meta-grid">
            <div class="meta-item">
                <span class="meta-label">ID предложения:</span>
                <span class="meta-value"><?php echo $proposal['id']; ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Дата создания:</span>
                <span class="meta-value"><?php echo date('d.m.Y H:i', strtotime($proposal['created_at'])); ?></span>
            </div>
            <?php if (isset($proposal['updated_at'])): ?>
            <div class="meta-item">
                <span class="meta-label">Последнее обновление:</span>
                <span class="meta-value"><?php echo date('d.m.Y H:i', strtotime($proposal['updated_at'])); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Компонент поделиться -->
<?php include __DIR__ . '/../components/share.php'; ?>
