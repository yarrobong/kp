<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'КП Генератор - Создание коммерческих предложений'); ?></title>

    <!-- SEO Мета-теги -->
    <meta name="description" content="<?php echo htmlspecialchars($description ?? 'КП Генератор - современная система для автоматизации создания профессиональных коммерческих предложений. Управляйте товарами, формируйте КП и экспортируйте в PDF.'); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($keywords ?? 'КП, коммерческое предложение, генератор КП, автоматизация продаж, PDF предложения, управление товарами, CRM система'); ?>">
    <meta name="robots" content="<?php echo htmlspecialchars($robots ?? 'index, follow'); ?>">
    <meta name="author" content="КП Генератор">

    <!-- Open Graph для социальных сетей -->
    <meta property="og:type" content="<?php echo htmlspecialchars($og_type ?? 'website'); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($og_title ?? $title ?? 'КП Генератор - Создание коммерческих предложений'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($og_description ?? $description ?? 'КП Генератор - современная система для автоматизации создания профессиональных коммерческих предложений.'); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($og_url ?? 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($og_image ?? '/css/placeholder-product.svg'); ?>">
    <meta property="og:site_name" content="КП Генератор">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($og_title ?? $title ?? 'КП Генератор - Создание коммерческих предложений'); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($og_description ?? $description ?? 'КП Генератор - современная система для автоматизации создания профессиональных коммерческих предложений.'); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($og_image ?? '/css/placeholder-product.svg'); ?>">

    <!-- Верификация вебмастеров -->
    <?php
    // Загрузка верификационных кодов из конфигурационного файла (если существует)
    $webmasterConfig = [];
    $webmasterConfigPath = __DIR__ . '/../config/webmaster.php';
    if (file_exists($webmasterConfigPath)) {
        $webmasterConfig = require $webmasterConfigPath;
    }
    
    // Яндекс.Вебмастер верификация
    // Инструкция: Получите код верификации на https://webmaster.yandex.ru/
    // Вариант 1: Создайте config/webmaster.php и добавьте код туда
    // Вариант 2: Замените 'YOUR_YANDEX_VERIFICATION_CODE' ниже на ваш код верификации
    $yandexVerificationCode = $webmasterConfig['yandex'] ?? 'YOUR_YANDEX_VERIFICATION_CODE';
    if ($yandexVerificationCode !== 'YOUR_YANDEX_VERIFICATION_CODE' && !empty($yandexVerificationCode)) {
        echo '    <meta name="yandex-verification" content="' . htmlspecialchars($yandexVerificationCode) . '">' . "\n";
    }
    ?>
    
    <?php
    // Google Search Console верификация
    // Инструкция: Получите код верификации на https://search.google.com/search-console
    // Вариант 1: Создайте config/webmaster.php и добавьте код туда
    // Вариант 2: Замените 'YOUR_GOOGLE_VERIFICATION_CODE' ниже на ваш код верификации
    $googleVerificationCode = $webmasterConfig['google'] ?? '266e2486b25c8ba7';
    if ($googleVerificationCode !== 'YOUR_GOOGLE_VERIFICATION_CODE' && !empty($googleVerificationCode)) {
        echo '    <meta name="google-site-verification" content="' . htmlspecialchars($googleVerificationCode) . '">' . "\n";
    }
    ?>
    
    <?php
    // Bing Webmaster Tools верификация (опционально)
    // Инструкция: Получите код верификации на https://www.bing.com/webmasters
    // Вариант 1: Создайте config/webmaster.php и добавьте код туда
    // Вариант 2: Замените 'YOUR_BING_VERIFICATION_CODE' ниже на ваш код верификации
    $bingVerificationCode = $webmasterConfig['bing'] ?? 'YOUR_BING_VERIFICATION_CODE';
    if ($bingVerificationCode !== 'YOUR_BING_VERIFICATION_CODE' && !empty($bingVerificationCode)) {
        echo '    <meta name="msvalidate.01" content="' . htmlspecialchars($bingVerificationCode) . '">' . "\n";
    }
    ?>

    <!-- Структурированные данные JSON-LD -->
    <script type="application/ld+json">
    <?php
    $jsonLd = [
        "@context" => "https://schema.org",
        "@type" => "WebApplication",
        "name" => "КП Генератор",
        "description" => "Современная система для автоматизации создания профессиональных коммерческих предложений",
        "url" => "https://example.com",
        "applicationCategory" => "BusinessApplication",
        "operatingSystem" => "Web Browser",
        "offers" => [
            "@type" => "Offer",
            "price" => "0",
            "priceCurrency" => "RUB",
            "description" => "Бесплатное использование системы создания коммерческих предложений"
        ],
        "creator" => [
            "@type" => "Organization",
            "name" => "КП Генератор",
            "url" => "https://example.com"
        ],
        "featureList" => [
            "Управление каталогом товаров",
            "Генерация коммерческих предложений",
            "Экспорт в PDF формат",
            "Отслеживание статусов предложений",
            "Поиск и фильтрация"
        ]
    ];

    // Если есть дополнительные данные (товар/предложение), добавляем их
    if (isset($structured_data) && is_array($structured_data)) {
        $jsonLd = array_merge($jsonLd, $structured_data);
    }

    echo json_encode($jsonLd, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    ?>
    </script>

    <!-- Дополнительные мета-теги -->
    <meta name="theme-color" content="#1a1a1a">
    <meta name="msapplication-TileColor" content="#1a1a1a">
    <meta name="format-detection" content="telephone=no">

    <!-- Подключение стилей -->
    <link rel="stylesheet" href="/css/app.css">

    <!-- Иконки -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
</head>
<body>
    <!-- Навигационное меню -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand" title="Главная страница КП Генератор">КП Генератор</a>
            <div class="navbar-menu">
                <?php
                $user = \Controllers\AuthController::getCurrentUser();
                if ($user) {
                    echo '<a href="/products" class="' . isActivePage('/products') . '" title="Управление товарами">Товары</a>';
                    echo '<a href="/proposals" class="' . isActivePage('/proposals') . '" title="Коммерческие предложения">КП</a>';

                    // Выпадающее меню пользователя
                    echo '<div class="user-menu">';
                    echo '<div class="user-menu-trigger">';
                    echo '<div class="user-avatar">' . strtoupper(substr(htmlspecialchars($user['name']), 0, 1)) . '</div>';
                    echo '<span class="user-name">' . htmlspecialchars($user['name']) . '</span>';
                    echo '<svg class="dropdown-arrow" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">';
                    echo '<path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>';
                    echo '</svg>';
                    echo '</div>';
                    echo '<div class="user-menu-dropdown">';
                    echo '<div class="user-info">';
                    echo '<div class="user-avatar-large">' . strtoupper(substr(htmlspecialchars($user['name']), 0, 1)) . '</div>';
                    echo '<div class="user-details">';
                    echo '<div class="user-fullname">' . htmlspecialchars($user['name']) . '</div>';
                    echo '<div class="user-role">' . ($user['role'] === 'admin' ? 'Администратор' : 'Пользователь') . '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '<hr class="menu-divider">';
                    echo '<a href="/user" class="menu-item">';
                    echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
                    echo '<path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                    echo '</svg>';
                    echo 'Личный кабинет';
                    echo '</a>';
                    if ($user['role'] === 'admin') {
                        echo '<a href="/admin" class="menu-item">';
                        echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
                        echo '<path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                        echo '</svg>';
                        echo 'Админ панель';
                        echo '</a>';
                    }
                    echo '<a href="/products" class="menu-item">';
                    echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
                    echo '<path d="M20 7L12 13L4 7M4 7H20M4 7L4 17C4 17.5304 4.21071 18.0391 4.58579 18.4142C4.96086 18.7893 5.46957 19 6 19H18C18.5304 19 19.0391 18.7893 19.4142 18.4142C19.7893 18.0391 20 17.5304 20 17V7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                    echo '</svg>';
                    echo 'Мои товары';
                    echo '</a>';
                    echo '<a href="/proposals" class="menu-item">';
                    echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
                    echo '<path d="M9 12H15M9 16H15M17 8H7C5.89543 8 5 8.89543 5 10V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V10C19 8.89543 18.1046 8 17 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                    echo '<path d="M5 10V6C5 4.89543 5.89543 4 7 4H17C18.1046 4 19 4.89543 19 6V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                    echo '</svg>';
                    echo 'Мои предложения';
                    echo '</a>';
                    echo '<hr class="menu-divider">';
                    echo '<form method="POST" action="/logout" style="margin: 0;">';
                    echo '<button type="submit" class="logout-btn menu-item">';
                    echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
                    echo '<path d="M17 16L21 12M21 12L17 8M21 12H9M12 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                    echo '</svg>';
                    echo 'Выход';
                    echo '</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo '<a href="/#features" title="Возможности системы">Возможности</a>';
                    echo '<a href="/login" class="' . isActivePage('/login') . '" title="Войти в систему">Вход</a>';
                    echo '<a href="/register" class="' . isActivePage('/register') . '" title="Зарегистрировать аккаунт">Регистрация</a>';
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
