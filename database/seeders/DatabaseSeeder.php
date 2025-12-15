<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Template;
use App\Support\Hash;

class DatabaseSeeder
{
    public function run()
    {
        // Создание администратора
        User::create([
            'name' => 'Администратор',
            'email' => 'admin@example.com',
            'password' => 'admin123',
            'role' => 'admin',
        ]);

        // Создание тестового пользователя
        User::create([
            'name' => 'Тестовый пользователь',
            'email' => 'user@example.com',
            'password' => 'user123',
            'role' => 'user',
        ]);

        // Создание системного шаблона
        Template::create([
            'user_id' => 1,
            'title' => 'Базовый шаблон КП',
            'description' => 'Стандартный шаблон коммерческого предложения',
            'body_html' => '<h1>Коммерческое предложение</h1><p>Уважаемый {company_name}!</p><p>Предлагаем вам следующие товары и услуги:</p>',
            'variables' => ['company_name', 'price'],
            'is_system' => true,
            'is_published' => true,
        ]);
    }
}



