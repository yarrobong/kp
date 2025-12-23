<div class="error-page error-404">
    <div class="container">
        <div class="error-content">
            <div class="error-code">404</div>
            <h1 class="error-title">Страница не найдена</h1>
            <p class="error-description">
                Извините, запрашиваемая страница не существует или была перемещена.
                Возможно, вы ввели неправильный адрес или страница была удалена.
            </p>

            <div class="error-actions">
                <a href="/" class="btn btn-primary btn-large" title="Вернуться на главную страницу">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 12L5 10M5 10L12 3L19 10M5 10V20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V10M9 22V14C9 13.4696 9.21071 12.9609 9.58579 12.5858C9.96086 12.2107 10.4696 12 11 12H13C13.5304 12 14.0391 12.2107 14.4142 12.5858C14.7893 12.9609 15 13.4696 15 14V22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    На главную
                </a>

                <a href="/products" class="btn btn-secondary" title="Посмотреть каталог товаров">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 7L12 13L4 7M4 7H20M4 7L4 17C4 17.5304 4.21071 18.0391 4.58579 18.4142C4.96086 18.7893 5.46957 19 6 19H18C18.5304 19 19.0391 18.7893 19.4142 18.4142C19.7893 18.0391 20 17.5304 20 17V7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Товары
                </a>

                <a href="javascript:history.back()" class="btn btn-secondary" title="Вернуться на предыдущую страницу">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Назад
                </a>
            </div>

            <div class="error-search">
                <h3>Поиск по сайту</h3>
                <form action="/products" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Поиск товаров..." class="search-input" required>
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 21L16.5 16.5M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Найти
                    </button>
                </form>
            </div>

            <div class="error-sitemap">
                <h3>Карта сайта</h3>
                <div class="sitemap-links">
                    <a href="/">Главная</a>
                    <a href="/products">Товары</a>
                    <a href="/#features">Возможности</a>
                    <a href="/#how-to-use">Как использовать</a>
                    <a href="/register">Регистрация</a>
                    <a href="/login">Вход</a>
                </div>
            </div>
        </div>

        <div class="error-visual">
            <div class="error-illustration">
                <svg width="300" height="200" viewBox="0 0 300 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="150" cy="100" r="80" fill="rgba(76, 175, 80, 0.1)" stroke="#4CAF50" stroke-width="2"/>
                    <circle cx="150" cy="100" r="60" fill="rgba(76, 175, 80, 0.05)" stroke="#4CAF50" stroke-width="1"/>
                    <text x="150" y="95" text-anchor="middle" font-family="Arial, sans-serif" font-size="48" fill="#4CAF50" font-weight="bold">404</text>
                    <text x="150" y="115" text-anchor="middle" font-family="Arial, sans-serif" font-size="14" fill="#666">Страница не найдена</text>
                </svg>
            </div>
        </div>
    </div>
</div>

<script>
// Автофокус на поле поиска при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.focus();
    }
});
</script>
