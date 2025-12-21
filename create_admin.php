<?php
require_once 'vendor/autoload.php';
require_once 'config/database.php';

use Models\User;

$admin = User::findByEmail('admin@example.com');
if (!$admin) {
    $userId = User::createUser([
        'name' => 'Administrator',
        'email' => 'admin@example.com',
        'password' => 'admin123',
        'role' => 'admin'
    ]);
    echo "Администратор создан!\n";
    echo "Email: admin@example.com\n";
    echo "Пароль: admin123\n";
} else {
    echo "Администратор уже существует\n";
}
