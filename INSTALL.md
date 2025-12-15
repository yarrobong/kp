# Инструкция по установке

## Быстрый старт

1. **Создайте базу данных:**
```sql
CREATE DATABASE commercial_proposals CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. **Выполните миграции:**
Импортируйте SQL файлы из `database/migrations/` в следующем порядке:
- 2024_01_01_000001_create_users_table.php
- 2024_01_01_000002_create_templates_table.php
- 2024_01_01_000003_create_proposals_table.php
- 2024_01_01_000004_create_proposal_files_table.php
- 2024_01_01_000005_create_personal_access_tokens_table.php

Или выполните SQL вручную, преобразовав миграции в SQL запросы.

3. **Настройте .env файл:**
Скопируйте `.env.example` в `.env` и заполните параметры БД.

4. **Создайте первого администратора:**
```sql
INSERT INTO users (name, email, password, role, created_at, updated_at) 
VALUES ('Admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW());
```
Пароль по умолчанию: `password`

5. **Запустите сервер:**
```bash
php -S localhost:8000 -t public
```

6. **Откройте в браузере:**
http://localhost:8000

## Настройка на хостинге

1. Загрузите все файлы на сервер
2. Настройте виртуальный хост, указывающий на папку `public/`
3. Убедитесь, что PHP версии 8.1+
4. Настройте права доступа:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Установка зависимостей через Composer

Если у вас установлен Composer:
```bash
composer install
```

Это установит:
- dompdf/dompdf - для генерации PDF
- phpoffice/phpword - для генерации DOCX
- spatie/laravel-permission - для управления ролями (опционально)



