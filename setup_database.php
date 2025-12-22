<?php
/**
 * Скрипт настройки базы данных для системы КП Генератор
 * Создает базу данных, пользователя и необходимые таблицы
 */

// Конфигурация подключения к MySQL (root пользователь)
$rootHost = 'localhost';
$rootUser = 'root';
$rootPassword = ''; // Оставьте пустым если пароль не установлен

// Конфигурация приложения
$appDbName = 'commercial_proposals';
$appUser = 'appuser';
$appPassword = 'apppassword';

echo "🚀 Начинаем настройку базы данных...\n\n";

try {
    // Подключение к MySQL как root
    echo "1. Подключение к MySQL...\n";
    $pdo = new PDO("mysql:host=$rootHost", $rootUser, $rootPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Подключение успешно\n\n";

    // Создание базы данных
    echo "2. Создание базы данных '$appDbName'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$appDbName` CHARACTER SET utf8 COLLATE utf8_general_ci");
    echo "✅ База данных создана\n\n";

    // Выбор базы данных
    $pdo->exec("USE `$appDbName`");

    // Создание пользователя
    echo "3. Создание пользователя '$appUser'...\n";
    $pdo->exec("CREATE USER IF NOT EXISTS '$appUser'@'localhost' IDENTIFIED BY '$appPassword'");
    $pdo->exec("GRANT ALL PRIVILEGES ON `$appDbName`.* TO '$appUser'@'localhost'");
    $pdo->exec("FLUSH PRIVILEGES");
    echo "✅ Пользователь создан и права предоставлены\n\n";

    // Создание таблицы пользователей
    echo "4. Создание таблицы пользователей...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "✅ Таблица пользователей создана\n";

    // Создание таблицы товаров
    echo "5. Создание таблицы товаров...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL DEFAULT 0,
            category VARCHAR(100),
            image VARCHAR(255),
            user_id INT NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_category (category)
        )
    ");
    echo "✅ Таблица товаров создана\n";

    // Создание таблицы предложений
    echo "6. Создание таблицы предложений...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS proposals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            offer_number VARCHAR(50) UNIQUE,
            offer_date DATE,
            client_info TEXT,
            total DECIMAL(10,2) NOT NULL DEFAULT 0,
            status VARCHAR(50) DEFAULT 'draft',
            user_id INT NOT NULL DEFAULT 1,
            template_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_status (status),
            INDEX idx_offer_number (offer_number)
        )
    ");
    echo "✅ Таблица предложений создана\n\n";

    // Добавление тестового администратора
    echo "7. Добавление тестового администратора...\n";
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO users (username, email, password, role) VALUES (?, ?, ?, ?)
    ");
    $stmt->execute(['admin', 'admin@example.com', $hashedPassword, 'admin']);
    echo "✅ Тестовый администратор добавлен\n";
    echo "   Логин: admin\n";
    echo "   Пароль: admin123\n\n";

    // Добавление тестового пользователя
    echo "8. Добавление тестового пользователя...\n";
    $hashedPassword = password_hash('user123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO users (username, email, password, role) VALUES (?, ?, ?, ?)
    ");
    $stmt->execute(['user', 'user@example.com', $hashedPassword, 'user']);
    echo "✅ Тестовый пользователь добавлен\n";
    echo "   Логин: user\n";
    echo "   Пароль: user123\n\n";

    echo "🎉 Настройка базы данных завершена успешно!\n\n";

    echo "📋 Резюме:\n";
    echo "- База данных: $appDbName\n";
    echo "- Пользователь: $appUser\n";
    echo "- Пароль: $appPassword\n\n";

    echo "🔍 Тестовые аккаунты:\n";
    echo "- Admin: admin / admin123\n";
    echo "- User: user / user123\n\n";

    echo "💡 Теперь вы можете запустить приложение!\n";

} catch (PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n\n";

    echo "🔧 Возможные решения:\n";
    echo "1. Убедитесь, что MySQL сервер запущен\n";
    echo "2. Проверьте пароль root пользователя MySQL\n";
    echo "3. Установите MySQL если он не установлен\n\n";

    echo "💡 Альтернатива: система будет работать с JSON файлами\n";
    exit(1);
}
?>
