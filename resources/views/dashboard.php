@extends('layouts.app')

@section('title', 'Дашборд')

@section('content')
<div class="page-header">
    <h1>Добро пожаловать в систему!</h1>
</div>

<!-- Метрики -->
<div class="dashboard-metrics">
    <div class="metric-card">
        <div class="metric-icon">📦</div>
        <div class="metric-value">{{ $stats['products'] ?? 0 }}</div>
        <div class="metric-label">Товаров в каталоге</div>
    </div>

    <div class="metric-card">
        <div class="metric-icon">📄</div>
        <div class="metric-value">{{ $stats['proposals'] ?? 0 }}</div>
        <div class="metric-label">Коммерческих предложений</div>
    </div>

    <div class="metric-card">
        <div class="metric-icon">🎨</div>
        <div class="metric-value">{{ $stats['templates'] ?? 0 }}</div>
        <div class="metric-label">Шаблонов</div>
    </div>
</div>

<!-- Быстрые действия -->
<div class="quick-actions">
    <div class="action-card" onclick="window.location.href='/products'">
        <div class="action-icon">📦</div>
        <div class="action-title">Управление товарами</div>
        <div class="action-description">Добавляйте и редактируйте товары в вашем каталоге</div>
    </div>

    <div class="action-card" onclick="window.location.href='/proposals'">
        <div class="action-icon">📄</div>
        <div class="action-title">Коммерческие предложения</div>
        <div class="action-description">Создавайте и управляйте КП на основе товаров</div>
    </div>

    <div class="action-card" onclick="window.location.href='/templates'">
        <div class="action-icon">🎨</div>
        <div class="action-title">Шаблоны</div>
        <div class="action-description">Настраивайте внешний вид ваших предложений</div>
    </div>

    @if(session('user_role') === 'admin')
    <div class="action-card" onclick="window.location.href='/admin'" style="border-left: 4px solid #667eea;">
        <div class="action-icon">⚙️</div>
        <div class="action-title">Админ панель</div>
        <div class="action-description">Управление пользователями и системой</div>
    </div>
    @endif
</div>

<!-- Последние действия -->
<div class="recent-activity">
    <h2 style="margin-bottom: 20px; color: #1a1a2e;">Последние действия</h2>

    <div class="activity-list">
        <div class="activity-item">
            <div class="activity-icon">🔐</div>
            <div class="activity-content">
                <div class="activity-title">Вход в систему</div>
                <div class="activity-time">{{ date('d.m.Y H:i') }}</div>
            </div>
        </div>

        <div class="activity-item">
            <div class="activity-icon">📊</div>
            <div class="activity-content">
                <div class="activity-title">Просмотр дашборда</div>
                <div class="activity-time">{{ date('d.m.Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
.recent-activity {
    margin-top: 40px;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.18);
}

.activity-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.activity-content {
    flex-grow: 1;
}

.activity-title {
    font-weight: 600;
    color: #1a1a2e;
    margin-bottom: 4px;
}

.activity-time {
    font-size: 14px;
    color: #4a5568;
}
</style>
