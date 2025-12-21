<?php

// Скрипт для создания админа с правильной ролью
// Запуск: php create-admin-user.php

require_once __DIR__ . '/bootstrap/app.php';

use App\Models\User;

echo "Создание администратора...\n";

try {
    // Проверяем, существует ли уже админ
    $existingAdmin = User::where('role', 'admin')->first();
    if ($existingAdmin) {
        echo "Админ уже существует:\n";
        echo "Email: " . $existingAdmin->email . "\n";
        echo "Пароль: password\n";
        exit;
    }

    // Создаем нового админа
    $admin = User::create([
        'name' => 'Администратор',
        'email' => 'admin@example.com',
        'password' => password_hash('password', PASSWORD_DEFAULT),
        'role' => 'admin',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    echo "✅ Админ успешно создан!\n";
    echo "Email: admin@example.com\n";
    echo "Пароль: password\n";
    echo "Роль: admin\n";

} catch (Exception $e) {
    echo "❌ Ошибка создания админа: " . $e->getMessage() . "\n";
}
