<div class="page-header">
    <h1>Мои предложения</h1>
    <div class="header-actions">
        <a href="/proposals/create" class="btn btn-primary">➕ Создать КП</a>
        <a href="/user" class="btn btn-secondary">← Личный кабинет</a>
    </div>
</div>

<?php if (empty($proposals)): ?>
    <div class="empty-state">
        <div class="empty-state-icon"></div>
        <h2>У вас пока нет предложений</h2>
        <p>Создайте свое первое коммерческое предложение</p>
        <a href="/proposals/create" class="btn btn-primary">Создать первое КП</a>
    </div>
<?php else: ?>
    <!-- Статистика предложений -->
    <div class="stats-bar">
        <div class="stat-item">
            <span class="stat-value"><?php echo count($proposals); ?></span>
            <span class="stat-label">Всего предложений</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">
                <?php
                $statusCounts = array_count_values(array_column($proposals, 'status'));
                echo $statusCounts['sent'] ?? 0;
                ?>
            </span>
            <span class="stat-label">Отправленных</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">
                <?php
                $totalAmount = array_sum(array_column($proposals, 'total'));
                echo number_format($totalAmount, 0, ',', ' ');
                ?> ₽
            </span>
            <span class="stat-label">Общая сумма</span>
        </div>
    </div>

    <!-- Фильтры и поиск -->
    <div class="filters-section">
        <div class="filters-row">
            <div class="search-box">
                <input type="text" id="proposalSearch" placeholder="🔍 Поиск предложений..." class="form-input">
            </div>
            <div class="status-filter">
                <select id="statusFilter" class="form-input">
                    <option value="">Все статусы</option>
                    <option value="draft">Черновики</option>
                    <option value="sent">Отправленные</option>
                    <option value="accepted">Принятые</option>
                    <option value="rejected">Отклоненные</option>
                </select>
            </div>
            <div class="sort-select">
                <select id="proposalSort" class="form-input">
                    <option value="date-desc">Сначала новые</option>
                    <option value="date-asc">Сначала старые</option>
                    <option value="title">По названию</option>
                    <option value="total-desc">По сумме (убыв.)</option>
                    <option value="total-asc">По сумме (возр.)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Список предложений -->
    <div class="proposals-list" id="proposalsList">
        <?php
        $statusLabels = [
            'draft' => 'Черновик',
            'sent' => 'Отправлено',
            'accepted' => 'Принято',
            'rejected' => 'Отклонено'
        ];

        $statusClasses = [
            'draft' => 'secondary',
            'sent' => 'primary',
            'accepted' => 'success',
            'rejected' => 'danger'
        ];
        ?>

        <?php foreach ($proposals as $proposal): ?>
        <div class="proposal-card" data-proposal-id="<?php echo $proposal['id']; ?>"
             data-status="<?php echo $proposal['status']; ?>"
             data-created-at="<?php echo $proposal['created_at']; ?>"
             data-total="<?php echo $proposal['total']; ?>">

            <div class="proposal-header">
                <div class="proposal-title-section">
                    <h3><?php echo htmlspecialchars($proposal['title']); ?></h3>
                    <span class="status-badge status-<?php echo htmlspecialchars($proposal['status']); ?>">
                        <?php echo $statusLabels[$proposal['status']] ?? $proposal['status']; ?>
                    </span>
                </div>
                <div class="proposal-number">
                    № <?php echo htmlspecialchars($proposal['offer_number']); ?>
                </div>
            </div>

            <div class="proposal-meta">
                <div class="meta-row">
                    <span class="meta-label">Дата:</span>
                    <span class="meta-value"><?php echo htmlspecialchars($proposal['offer_date']); ?></span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Клиент:</span>
                    <span class="meta-value">
                        <?php echo htmlspecialchars($proposal['client_name'] ?? 'Не указан'); ?>
                    </span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Сумма:</span>
                    <span class="meta-value total-amount"><?php echo number_format($proposal['total'], 0, ',', ' '); ?> ₽</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Создано:</span>
                    <span class="meta-value"><?php echo date('d.m.Y H:i', strtotime($proposal['created_at'])); ?></span>
                </div>
            </div>

            <div class="proposal-actions">
                <a href="/proposals/<?php echo $proposal['id']; ?>" class="btn btn-small">👁 Просмотр</a>
                <?php if ($proposal['status'] === 'draft'): ?>
                <a href="/proposals/<?php echo $proposal['id']; ?>/edit" class="btn btn-small btn-secondary">✏ Редактировать</a>
                <?php endif; ?>
                <a href="/proposals/<?php echo $proposal['id']; ?>/pdf" class="btn btn-small btn-success" target="_blank">PDF</a>
                <?php if ($proposal['status'] === 'draft'): ?>
                <button onclick="deleteProposal(<?php echo $proposal['id']; ?>, '<?php echo htmlspecialchars($proposal['title']); ?>')"
                        class="btn btn-small btn-danger">🗑 Удалить</button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="deleteModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Подтверждение удаления</h3>
                <span class="modal-close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>Вы действительно хотите удалить предложение "<span id="deleteProposalTitle"></span>"?</p>
                <p class="warning-text">Это действие нельзя отменить.</p>
            </div>
            <div class="modal-footer">
                <button onclick="closeDeleteModal()" class="btn btn-secondary">Отмена</button>
                <button id="confirmDeleteBtn" onclick="confirmDelete()" class="btn btn-danger">Удалить</button>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
// Поиск предложений
document.getElementById('proposalSearch').addEventListener('input', function() {
    filterProposals();
});

// Фильтр по статусу
document.getElementById('statusFilter').addEventListener('change', function() {
    filterProposals();
});

// Сортировка предложений
document.getElementById('proposalSort').addEventListener('change', function() {
    sortProposals();
});

function filterProposals() {
    const searchTerm = document.getElementById('proposalSearch').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const cards = document.querySelectorAll('.proposal-card');

    cards.forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        const number = card.querySelector('.proposal-number').textContent.toLowerCase();
        const client = card.querySelector('.meta-value')?.textContent.toLowerCase() || '';
        const status = card.dataset.status;

        const matchesSearch = title.includes(searchTerm) ||
                             number.includes(searchTerm) ||
                             client.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;

        if (matchesSearch && matchesStatus) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });

    updateStats();
}

function sortProposals() {
    const sortBy = document.getElementById('proposalSort').value;
    const list = document.getElementById('proposalsList');
    const cards = Array.from(list.children);

    cards.sort((a, b) => {
        switch (sortBy) {
            case 'title':
                return a.querySelector('h3').textContent.localeCompare(
                    b.querySelector('h3').textContent);

            case 'total-desc':
                return parseFloat(b.dataset.total) - parseFloat(a.dataset.total);

            case 'total-asc':
                return parseFloat(a.dataset.total) - parseFloat(b.dataset.total);

            case 'date-desc':
                return new Date(b.dataset.createdAt) - new Date(a.dataset.createdAt);

            case 'date-asc':
                return new Date(a.dataset.createdAt) - new Date(b.dataset.createdAt);

            default:
                return 0;
        }
    });

    cards.forEach(card => list.appendChild(card));
}

function updateStats() {
    const visibleCards = document.querySelectorAll('.proposal-card:not([style*="display: none"])');
    const totalCount = visibleCards.length;
    const sentCount = Array.from(visibleCards).filter(card =>
        card.dataset.status === 'sent'
    ).length;
    const totalAmount = Array.from(visibleCards).reduce((sum, card) => {
        return sum + parseFloat(card.dataset.total);
    }, 0);

    // Обновляем статистику
    document.querySelectorAll('.stat-value')[0].textContent = totalCount;
    document.querySelectorAll('.stat-value')[1].textContent = sentCount;
    document.querySelectorAll('.stat-value')[2].textContent = totalAmount.toLocaleString('ru-RU') + ' ₽';
}

// Модальное окно удаления
let proposalToDelete = null;

function deleteProposal(id, title) {
    proposalToDelete = id;
    document.getElementById('deleteProposalTitle').textContent = title;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    proposalToDelete = null;
}

function confirmDelete() {
    if (!proposalToDelete) return;

    fetch(`/proposals/${proposalToDelete}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Удаляем карточку предложения
            const card = document.querySelector(`[data-proposal-id="${proposalToDelete}"]`);
            if (card) {
                card.remove();
            }

            // Обновляем счетчики
            updateStats();

            closeDeleteModal();
            showMessage('Предложение успешно удалено', 'success');
        } else {
            showMessage(data.message || 'Ошибка при удалении предложения', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Ошибка при удалении предложения', 'error');
    });
}

function showMessage(message, type) {
    // Создаем элемент уведомления
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">×</button>
    `;

    document.body.appendChild(notification);

    // Автоматически скрываем через 5 секунд
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Закрытие модального окна по клику вне его
window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        closeDeleteModal();
    }
}

// Инициализация
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
});
</script>
