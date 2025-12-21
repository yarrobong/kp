<?php
// Простой скрипт для создания таблиц базы данных
include 'index.php';

echo "Создание таблиц базы данных...\n";

$db = getDB();
if (!$db) {
    die("Не удалось подключиться к базе данных\n");
}

$tables = [
    // Таблица пользователей
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('guest', 'user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    // Таблица товаров
    "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT DEFAULT 1,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL DEFAULT 0,
        category VARCHAR(100),
        image VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    // Таблица предложений
    "CREATE TABLE IF NOT EXISTS proposals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL DEFAULT 1,
        template_id INT NULL,
        title VARCHAR(255) NOT NULL,
        offer_number VARCHAR(50) NOT NULL,
        offer_date DATE NOT NULL,
        client_info TEXT,
        status ENUM('draft', 'sent', 'accepted', 'rejected') DEFAULT 'draft',
        total DECIMAL(10,2) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )"
];

foreach ($tables as $tableName => $sql) {
    try {
        $db->exec($sql);
        echo "✓ Таблица создана/обновлена\n";
    } catch (Exception $e) {
        echo "✗ Ошибка создания таблицы: " . $e->getMessage() . "\n";
    }
}

// Создаем тестового пользователя
try {
    $stmt = $db->prepare("INSERT IGNORE INTO users (id, name, email, password, role) VALUES (1, 'Demo User', 'demo@example.com', ?, 'admin')");
    $stmt->execute([password_hash('demo', PASSWORD_DEFAULT)]);
    echo "✓ Тестовый пользователь создан\n";
} catch (Exception $e) {
    echo "✗ Ошибка создания пользователя: " . $e->getMessage() . "\n";
}

echo "Миграция завершена!\n";
