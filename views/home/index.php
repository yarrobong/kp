<!-- Якорное меню навигации -->
<nav class="anchor-nav">
    <div class="container">
        <a href="#features" title="Возможности системы">Возможности</a>
        <a href="#how-to-use" title="Как использовать">Инструкция</a>
        <a href="#stats" title="Статистика">Статистика</a>
        <a href="#get-started" title="Начать работу">Начать</a>
    </div>
</nav>

<div class="hero">
    <div class="container">
        <h1>
            <?php if ($user): ?>
                Добро пожаловать, <span class="highlight"><?php echo htmlspecialchars($user['name']); ?></span>!
            <?php else: ?>
                Добро пожаловать в <span class="highlight">КП Генератор</span>
            <?php endif; ?>
        </h1>
        <p class="hero-subtitle">
            <?php echo $user ? 'Управляйте своими товарами и предложениями в современном интерфейсе' : 'Профессиональный инструмент для создания коммерческих предложений с автоматическим расчетом'; ?>
        </p>
        <div class="hero-actions">
            <?php if ($user): ?>
                <a href="/products" class="btn btn-primary" title="Перейти к управлению товарами">
                    Мои товары
                </a>
                <a href="/proposals/create" class="btn btn-secondary" title="Создать новое коммерческое предложение">
                    Создать предложение
                </a>
                <a href="/user" class="btn btn-secondary" title="Личный кабинет пользователя">
                    Личный кабинет
                </a>
            <?php else: ?>
                <a href="#features" class="btn btn-primary" title="Посмотреть возможности системы">
                    Возможности системы
                </a>
                <a href="/register" class="btn btn-secondary" title="Зарегистрировать новый аккаунт">
                    Регистрация
                </a>
                <a href="/login" class="btn btn-secondary" title="Войти в существующий аккаунт">
                    <span class="btn-icon">🔑</span>
                    Вход
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<section id="features" class="features">
    <div class="container">
        <h2>Возможности системы</h2>
        <div class="features-grid">
            <div class="feature-card">
                <h3>Управление товарами</h3>
                <p>Добавляйте, редактируйте и управляйте каталогом товаров с подробными описаниями, ценами и изображениями.</p>
            </div>
            <div class="feature-card">
                <h3>Генерация КП</h3>
                <p>Создавайте профессиональные коммерческие предложения с автоматическим расчетом стоимости и экспортом в PDF.</p>
            </div>
            <div class="feature-card">
                <h3>📊 Отчеты и статистика</h3>
                <p>Отслеживайте созданные предложения, их статусы и общую статистику продаж в реальном времени.</p>
            </div>
            <div class="feature-card">
                <h3>🔍 Поиск и фильтры</h3>
                <p>Быстрый поиск товаров и предложений с гибкими фильтрами по категориям, статусам и датам.</p>
            </div>
            <div class="feature-card">
                <h3>🎨 Современный интерфейс</h3>
                <p>Темная тема, адаптивный дизайн и интуитивно понятная навигация для комфортной работы.</p>
            </div>
            <div class="feature-card">
                <h3>📱 Мобильная версия</h3>
                <p>Полная поддержка мобильных устройств с оптимизированным интерфейсом для планшетов и смартфонов.</p>
            </div>
        </div>
    </div>
</section>

<section id="how-to-use" class="how-to-use">
    <div class="container">
        <h2>⚡ Как использовать систему</h2>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Добавьте товары</h3>
                <p>Создайте каталог товаров с названиями, описаниями, ценами, категориями и изображениями.</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>🔍 Выберите товары</h3>
                <p>Используйте умный поиск для быстрого нахождения товаров и укажите количество.</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Создайте предложение</h3>
                <p>Укажите данные клиента, и система автоматически сгенерирует профессиональное КП.</p>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <h3>📤 Экспортируйте в PDF</h3>
                <p>Скачайте готовое предложение в формате PDF с автоматическим расчетом суммы.</p>
            </div>
        </div>
    </div>
</section>

<section id="stats" class="stats">
    <div class="container">
        <h2>📊 Статистика системы</h2>
        <div class="stats-grid">
            <div class="stat">
                <div class="stat-number" id="products-count">10+</div>
                <div class="stat-label">Товаров в каталоге</div>
            </div>
            <div class="stat">
                <div class="stat-number" id="proposals-count">-</div>
                <div class="stat-label">Созданных предложений</div>
            </div>
            <div class="stat">
                <div class="stat-number" id="total-value">500K+ ₽</div>
                <div class="stat-label">Общая сумма предложений</div>
            </div>
        </div>
    </div>
</section>

<section id="get-started" class="footer-cta">
    <div class="container">
        <h2>Готовы начать работу?</h2>
        <p>Создайте свое первое профессиональное коммерческое предложение прямо сейчас</p>
        <a href="/proposals/create" class="btn btn-primary btn-large" title="Создать первое коммерческое предложение">
            <span class="btn-icon">⚡</span>
            Создать предложение
        </a>
        <div class="additional-links">
            <a href="/products" title="Посмотреть каталог товаров">Каталог товаров</a>
            <a href="/register" title="Зарегистрировать аккаунт">Регистрация</a>
            <a href="/login" title="Войти в систему">Вход</a>
        </div>
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

// Якорное меню - подсветка активной секции
function initAnchorNav() {
    const anchorLinks = document.querySelectorAll('.anchor-nav a');
    const sections = document.querySelectorAll('section[id]');

    function highlightActiveSection() {
        const scrollPosition = window.scrollY + 100;

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');

            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                anchorLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${sectionId}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }

    // Плавная прокрутка к секциям
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);

            if (targetSection) {
                const offsetTop = targetSection.offsetTop - 80; // Учитываем высоту меню
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Обновление активной секции при скролле
    window.addEventListener('scroll', highlightActiveSection);
    highlightActiveSection(); // Вызываем сразу для начальной позиции
}

// Анимации при появлении элементов
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    // Наблюдаем за секциями
    document.querySelectorAll('section').forEach(section => {
        observer.observe(section);
    });

    // Наблюдаем за карточками фич
    document.querySelectorAll('.feature-card').forEach((card, index) => {
        card.style.transitionDelay = `${index * 0.1}s`;
        observer.observe(card);
    });
}

// Запуск после загрузки DOM
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    initAnchorNav();
    initScrollAnimations();
});
</script>
