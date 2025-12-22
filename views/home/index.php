<div class="hero">
    <div class="container">
        <h1><?php echo $user ? 'Добро пожаловать, ' . htmlspecialchars($user['name']) . '!' : 'Добро пожаловать в КП Генератор'; ?></h1>
        <p class="hero-subtitle">
            <?php echo $user ? 'Управляйте своими товарами и предложениями' : 'Профессиональный инструмент для создания коммерческих предложений'; ?>
        </p>
        <div class="hero-actions">
            <?php if ($user): ?>
                <a href="/products" class="btn btn-primary">Мои товары</a>
                <a href="/proposals/create" class="btn btn-secondary">Создать предложение</a>
                <a href="/user" class="btn btn-secondary">Личный кабинет</a>
            <?php else: ?>
                <a href="/products" class="btn btn-primary">Просмотреть товары</a>
                <a href="/register" class="btn btn-secondary">Регистрация</a>
                <a href="/login" class="btn btn-secondary">Вход</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<section class="features">
    <div class="container">
        <h2>Возможности системы</h2>
        <div class="features-grid">
            <div class="feature-card">
                <h3>📦 Управление товарами</h3>
                <p>Добавляйте, редактируйте и управляйте каталогом товаров с подробными описаниями и ценами.</p>
            </div>
            <div class="feature-card">
                <h3>📄 Генерация КП</h3>
                <p>Создавайте профессиональные коммерческие предложения с автоматическим расчетом стоимости.</p>
            </div>
            <div class="feature-card">
                <h3>📊 Отчеты и статистика</h3>
                <p>Отслеживайте созданные предложения, их статусы и общую статистику продаж.</p>
            </div>
            <div class="feature-card">
                <h3>📋 Поиск и фильтры</h3>
                <p>Быстрый поиск товаров и предложений с гибкими фильтрами по категориям и статусам.</p>
            </div>
        </div>
    </div>
</section>

<section class="how-to-use">
    <div class="container">
        <h2>Как использовать</h2>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Добавьте товары</h3>
                <p>Создайте каталог товаров с названиями, описаниями, ценами и категориями.</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>Выберите товары</h3>
                <p>Выберите необходимые товары и укажите количество для каждого товара.</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Создайте предложение</h3>
                <p>Укажите данные клиента и автоматически сгенерируйте коммерческое предложение.</p>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <h3>Экспортируйте в PDF</h3>
                <p>Скачайте готовое предложение в формате PDF для отправки клиенту.</p>
            </div>
        </div>
    </div>
</section>

<section class="stats">
    <div class="container">
        <h2>Статистика системы</h2>
        <div class="stats-grid">
            <div class="stat">
                <div class="stat-number" id="products-count">-</div>
                <div class="stat-label">Товаров в каталоге</div>
            </div>
            <div class="stat">
                <div class="stat-number" id="proposals-count">-</div>
                <div class="stat-label">Созданных предложений</div>
            </div>
            <div class="stat">
                <div class="stat-number" id="total-value">-</div>
                <div class="stat-label">Общая сумма предложений</div>
            </div>
        </div>
    </div>
</section>

<section class="footer-cta">
    <div class="container">
        <h2>Готовы начать?</h2>
        <p>Создайте свое первое коммерческое предложение прямо сейчас</p>
        <a href="/proposals/create" class="btn btn-primary btn-large">Создать предложение</a>
    </div>
</section>

<script>
// Загрузка статистики
async function loadStats() {
    try {
        const response = await fetch('/health');
        const data = await response.json();

        if (data.database && data.database.includes('proposals:')) {
            const proposalsMatch = data.database.match(/proposals:\s*(\d+)/);
            if (proposalsMatch) {
                document.getElementById('proposals-count').textContent = proposalsMatch[1];
            }
        }

        // Заглушка для других статистик
        document.getElementById('products-count').textContent = '10+';
        document.getElementById('total-value').textContent = '500K+ ₽';

    } catch (error) {
        console.error('Ошибка загрузки статистики:', error);
    }
}

// Запуск после загрузки DOM
document.addEventListener('DOMContentLoaded', loadStats);
</script>
