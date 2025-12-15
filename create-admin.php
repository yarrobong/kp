<?php

require __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use App\Support\Hash;

try {
    echo "Создание администратора...\n";

    $user = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
        'role' => 'admin'
    ]);

    echo "✅ Администратор создан!\n";
    echo "Email: admin@example.com\n";
    echo "Пароль: password\n";

} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";

    // Попробуем через SQL
    echo "Пробуем создать через SQL...\n";

    $pdo = new PDO('mysql:host=localhost;dbname=commercial_proposals;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $password = password_hash('password', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->execute(['Admin', 'admin@example.com', $password, 'admin']);

    echo "✅ Администратор создан через SQL!\n";
    echo "Email: admin@example.com\n";
    echo "Пароль: password\n";
}

