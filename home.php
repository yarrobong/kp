<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>КП Генератор - Автоматизация коммерческих предложений</title>
    <link rel="stylesheet" href="/css/app.css">
    <style>
    .hero {
        text-align: center;
        padding: 80px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        margin-bottom: 60px;
    }

    .hero h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .hero p {
        font-size: 1.4rem;
        margin-bottom: 40px;
        opacity: 0.9;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }

    .cta-button {
        display: inline-block;
        background: #4CAF50;
        color: white;
        padding: 16px 32px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }

    .cta-button:hover {
        background: #45a049;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
    }

    .features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 60px;
    }

    .feature-card {
        background: #2d2d2d;
        padding: 30px;
        border-radius: 16px;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #333;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        border-color: #1976d2;
    }

    .feature-icon {
        font-size: 3rem;
        margin-bottom: 20px;
        display: block;
    }

    .feature-card h3 {
        color: #ffffff;
        margin-bottom: 15px;
        font-size: 1.3rem;
    }

    .feature-card p {
        color: #b0b0b0;
        line-height: 1.6;
    }

    .how-to-use {
        background: #1e1e1e;
        padding: 60px 20px;
        margin-bottom: 60px;
        border-radius: 16px;
    }

    .how-to-use h2 {
        text-align: center;
        color: #ffffff;
        margin-bottom: 40px;
        font-size: 2rem;
    }

    .steps {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        max-width: 1000px;
        margin: 0 auto;
    }

    .step {
        text-align: center;
        position: relative;
    }

    .step-number {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #1976d2, #42a5f5);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
        margin: 0 auto 20px;
        position: relative;
        z-index: 2;
    }

    .step h4 {
        color: #ffffff;
        margin-bottom: 10px;
        font-size: 1.1rem;
    }

    .step p {
        color: #b0b0b0;
        line-height: 1.5;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        text-align: center;
        margin-bottom: 60px;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #1976d2;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #b0b0b0;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .footer-cta {
        text-align: center;
        padding: 40px;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: 16px;
        color: white;
    }

    .footer-cta h3 {
        margin-bottom: 20px;
        font-size: 1.8rem;
    }

    .footer-cta p {
        margin-bottom: 30px;
        opacity: 0.9;
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .hero h1 {
            font-size: 2.5rem;
        }

        .hero p {
            font-size: 1.2rem;
        }

        .features {
            grid-template-columns: 1fr;
        }

        .steps {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">КП Генератор</a>
            <div class="navbar-menu">
                <a href="#features">Возможности</a>
                <a href="#how-to">Как пользоваться</a>
                <a href="/products">Товары</a>
                <a href="/proposals">КП</a>
            </div>
        </div>
    </nav>

    <div class="hero">
        <div class="container">
            <h1>КП Генератор</h1>
            <p>Автоматизируйте процесс создания коммерческих предложений. Управляйте каталогом товаров, формируйте профессиональные КП и экспортируйте их в PDF всего за несколько кликов.</p>
            <a href="/products" class="cta-button">🚀 Начать работу</a>
        </div>
    </div>

    <main class="container">
        <section id="features" class="features">
            <div class="feature-card">
                <span class="feature-icon">📦</span>
                <h3>Управление товарами</h3>
                <p>Создавайте и редактируйте каталог товаров с фотографиями, описаниями и ценами. Легко добавляйте новые позиции и обновляйте информацию.</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">📄</span>
                <h3>Генерация КП</h3>
                <p>Формируйте профессиональные коммерческие предложения из готового каталога. Выбирайте товары, указывайте количества и автоматически рассчитывайте суммы.</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">📊</span>
                <h3>Отслеживание статусов</h3>
                <p>Следите за статусом каждого предложения: черновик, отправлено, принято или отклонено. Управляйте всем процессом продаж в одном месте.</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">📋</span>
                <h3>Экспорт в PDF</h3>
                <p>Экспортируйте готовые коммерческие предложения в профессионально оформленный PDF с вашим логотипом и контактными данными.</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">🔍</span>
                <h3>Поиск и фильтры</h3>
                <p>Быстро находите нужные товары с помощью умного поиска и фильтров по категориям, цене и другим параметрам.</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">📱</span>
                <h3>Адаптивный дизайн</h3>
                <p>Работайте с системой на любом устройстве - от компьютера до смартфона. Интерфейс автоматически адаптируется под размер экрана.</p>
            </div>
        </section>

        <section class="stats">
            <div>
                <div class="stat-number">∞</div>
                <div class="stat-label">товаров в каталоге</div>
            </div>
            <div>
                <div class="stat-number">∞</div>
                <div class="stat-label">созданных КП</div>
            </div>
            <div>
                <div class="stat-number">0</div>
                <div class="stat-label">часов на оформление</div>
            </div>
            <div>
                <div class="stat-number">100%</div>
                <div class="stat-label">автоматизация</div>
            </div>
        </section>

        <section id="how-to" class="how-to-use">
            <h2>Как пользоваться системой</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h4>Создайте каталог товаров</h4>
                    <p>Добавьте все ваши товары с фотографиями, описаниями, ценами и характеристиками. Система поддерживает категории для удобной организации.</p>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <h4>Сформируйте предложение</h4>
                    <p>Выберите товары из каталога, укажите количества для каждого товара. Система автоматически рассчитает общую сумму предложения.</p>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <h4>Экспортируйте в PDF</h4>
                    <p>Скачайте готовое коммерческое предложение в профессионально оформленном PDF формате с вашими контактными данными.</p>
                </div>

                <div class="step">
                    <div class="step-number">4</div>
                    <h4>Отслеживайте статусы</h4>
                    <p>Следите за тем, какие предложения отправлены клиентам, какие приняты, а какие отклонены. Ведите историю всех взаимодействий.</p>
                </div>
            </div>
        </section>

        <div class="footer-cta">
            <h3>Готовы автоматизировать процесс продаж?</h3>
            <p>Начните создавать профессиональные коммерческие предложения прямо сейчас!</p>
            <a href="/products" class="cta-button">Создать первое предложение →</a>
        </div>
    </main>

    <script>
    // Плавная прокрутка для якорных ссылок
    document.addEventListener("DOMContentLoaded", function() {
        const navbarLinks = document.querySelectorAll(".navbar-menu a[href^='#']");

        navbarLinks.forEach(link => {
            link.addEventListener("click", function(e) {
                e.preventDefault();

                const targetId = this.getAttribute("href").substring(1);
                const targetElement = document.getElementById(targetId);

                if (targetElement) {
                    const offsetTop = targetElement.offsetTop - 80; // Учитываем высоту навигации

                    window.scrollTo({
                        top: offsetTop,
                        behavior: "smooth"
                    });
                }
            });
        });

        // Добавляем эффект появления при прокрутке
        const observerOptions = {
            threshold: 0.1,
            rootMargin: "0px 0px -50px 0px"
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = "1";
                    entry.target.style.transform = "translateY(0)";
                }
            });
        }, observerOptions);

        // Наблюдаем за элементами
        document.querySelectorAll(".feature-card, .step").forEach(el => {
            el.style.opacity = "0";
            el.style.transform = "translateY(20px)";
            el.style.transition = "opacity 0.6s ease, transform 0.6s ease";
            observer.observe(el);
        });
    });
    </script>
</body>
</html>
