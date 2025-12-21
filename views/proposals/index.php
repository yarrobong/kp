<div class="page-header">
    <h1>Коммерческие предложения</h1>
    <a href="/proposals/create" class="btn btn-primary">Создать предложение</a>
</div>

<!-- Список предложений -->
<div class="proposals-list">
    <?php if (empty($proposals)): ?>
    <div class="empty-state">
        <h3>Предложения не найдены</h3>
        <p>Создайте свое первое коммерческое предложение</p>
        <a href="/proposals/create" class="btn btn-primary">Создать предложение</a>
    </div>
    <?php else: ?>
    <?php foreach ($proposals as $proposal): ?>
    <div class="proposal-card">
        <div class="proposal-header">
            <h3><?php echo htmlspecialchars($proposal['title']); ?></h3>
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

        <div class="proposal-meta">
            <span>Номер: <?php echo htmlspecialchars($proposal['offer_number']); ?></span>
            <span>Дата: <?php echo htmlspecialchars($proposal['offer_date']); ?></span>
            <span>Сумма: <?php echo number_format($proposal['total'], 0, ',', ' '); ?> ₽</span>
        </div>

        <?php
        $clientInfo = json_decode($proposal['client_info'], true);
        if ($clientInfo && isset($clientInfo['products'])): ?>
        <div class="proposal-products">
            <span>Товаров: <?php echo count($clientInfo['products']); ?> шт.</span>
        </div>
        <?php endif; ?>

        <div class="proposal-actions">
            <a href="/proposals/<?php echo $proposal['id']; ?>" class="btn btn-small">Просмотр</a>
            <a href="/proposals/<?php echo $proposal['id']; ?>/edit" class="btn btn-small btn-secondary">Редактировать</a>
            <a href="/proposals/<?php echo $proposal['id']; ?>/pdf" class="btn btn-small btn-success">PDF</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
