<?php
// Скрипт для создания первого администратора
require_once __DIR__ . '/models/User.php';

echo "Инициализация системы...\n";

// Создаем первого администратора
$adminData = [
    'name' => 'Administrator',
    'email' => 'admin@example.com',
    'password' => 'admin123',
    'role' => 'admin'
];

$userId = User::createUser($adminData);

if ($userId) {
    echo "✅ Администратор создан!\n";
    echo "Email: admin@example.com\n";
    echo "Пароль: admin123\n";
    echo "Пожалуйста, измените пароль после первого входа!\n";
} else {
    echo "❌ Ошибка при создании администратора\n";
}

echo "Инициализация завершена!\n";
