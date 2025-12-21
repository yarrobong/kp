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
                <a href="/products" class="<?php echo isActivePage('/products'); ?>">Товары</a>
                <a href="/proposals" class="<?php echo isActivePage('/proposals'); ?>">КП</a>
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
