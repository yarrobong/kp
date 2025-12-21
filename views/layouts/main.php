<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'КП Генератор'); ?></title>

    <!-- Подключение стилей -->
    <link rel="stylesheet" href="/css/app.css">

    <!-- Иконки -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
</head>
<body>
    <!-- Навигационное меню -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">КП Генератор</a>
            <div class="navbar-menu">
                <?php
                $user = \AuthController::getCurrentUser();
                if ($user) {
                    echo '<a href="/products" class="' . isActivePage('/products') . '">Товары</a>';
                    echo '<a href="/proposals" class="' . isActivePage('/proposals') . '">КП</a>';

                    // Выпадающее меню пользователя
                    echo '<div class="user-menu">';
                    echo '<button class="user-menu-btn">' . htmlspecialchars($user['name']) . ' ▼</button>';
                    echo '<div class="user-menu-dropdown">';
                    echo '<a href="/user">Личный кабинет</a>';
                    if ($user['role'] === 'admin') {
                        echo '<a href="/admin">Админ панель</a>';
                    }
                    echo '<hr>';
                    echo '<form method="POST" action="/logout" style="margin: 0;">';
                    echo '<button type="submit" class="logout-btn">Выход</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo '<a href="/login" class="' . isActivePage('/login') . '">Вход</a>';
                    echo '<a href="/register" class="' . isActivePage('/register') . '">Регистрация</a>';
                }
                ?>
            </div>
        </div>
    </nav>

    <!-- Основной контент -->
    <main class="main-content">
        <div class="container">
            <?php echo $content; ?>
        </div>
    </main>

    <!-- Подключение JavaScript -->
    <script src="/js/app.js"></script>

    <!-- Flash сообщения -->
    <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="notification <?php echo $_SESSION['flash_type'] ?? 'success'; ?>" id="flash-message">
        <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
        <button onclick="closeFlashMessage()">×</button>
    </div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>
</body>
</html>
