<!-- Hero секция предложений -->
<div class="proposals-hero">
    <h1>📋 Коммерческие предложения</h1>
    <p>Управляйте своими коммерческими предложениями, отслеживайте их статус и создавайте новые предложения для клиентов</p>
    <a href="/proposals/create" class="btn btn-primary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Создать предложение
    </a>
</div>

<!-- Список предложений -->
<div class="proposals-list">
    <?php if (empty($proposals)): ?>
    <div class="empty-state">
        <h3>📋 Предложения не найдены</h3>
        <p>Создайте свое первое коммерческое предложение и начните работать с клиентами</p>
        <a href="/proposals/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Создать первое предложение
        </a>
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
            <span>
                <strong><?php echo htmlspecialchars($proposal['offer_number']); ?></strong>
                Номер
            </span>
            <span>
                <strong><?php echo htmlspecialchars($proposal['offer_date']); ?></strong>
                Дата
            </span>
            <span>
                <strong><?php echo number_format($proposal['total'], 0, ',', ' '); ?> ₽</strong>
                Сумма
            </span>
        </div>

        <?php
        $clientInfo = json_decode($proposal['client_info'], true);
        if ($clientInfo && isset($clientInfo['products'])): ?>
        <div class="proposal-products">
            <span>Товаров: <?php echo count($clientInfo['products']); ?> шт.</span>
        </div>
        <?php endif; ?>

        <div class="proposal-actions">
            <a href="/proposals/<?php echo $proposal['id']; ?>" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M2.4578 12C3.73207 7.94281 7.52236 5 12 5C16.4776 5 20.2679 7.94281 21.5422 12C20.2679 16.0572 16.4776 19 12 19C7.52236 19 3.73207 16.0572 2.4578 12Z" stroke="currentColor" stroke-width="2"/>
                </svg>
                Просмотр
            </a>
            <a href="/proposals/<?php echo $proposal['id']; ?>/edit" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3 20.5304 3 20V6C3 5.46957 3.21071 5 3.58579 5C3.96086 4.78929 4.46957 4.78929 4.58579 4.58579C4.96086 4.21071 5 4 5 4V20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H21C21.5304 22 22.0391 21.7893 22.4142 21.4142C22.7893 21.0391 23 20.5304 23 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18.5 2.5C18.8978 2.10217 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.10217 21.5 2.5C21.8978 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8978 5.10217 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Редактировать
            </a>
            <a href="/proposals/<?php echo $proposal['id']; ?>/pdf" class="btn btn-success">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 13H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 17H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10 9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                PDF
            </a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
