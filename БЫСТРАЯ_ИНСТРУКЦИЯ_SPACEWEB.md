# 🚀 БЫСТРАЯ ИНСТРУКЦИЯ: Загрузка на Spaceweb

## ⚡ Что нужно сделать (по порядку):

---

## ШАГ 1: Подготовка файлов (2 минуты)

### 1.1. Проверьте, что у вас есть все файлы проекта
Должны быть папки: `app`, `public`, `config`, `database`, `resources`, `routes`

### 1.2. Установите FileZilla (если нет)
- Скачайте: https://filezilla-project.org/download.php?type=client
- Установите программу

---

## ШАГ 2: Получение данных FTP (3 минуты)

### 2.1. Войдите в панель Spaceweb
- Откройте https://spaceweb.ru
- Войдите в личный кабинет
- Откройте "Панель управления" или "ISPmanager"

### 2.2. Найдите данные FTP
Ищите раздел: **"FTP"** или **"Файлы"** или **"Файловый менеджер"**

**Запишите в блокнот:**
- FTP хост: `________________`
- FTP логин: `________________`
- FTP пароль: `________________`

---

## ШАГ 3: Подключение через FileZilla (2 минуты)

### 3.1. Откройте FileZilla

### 3.2. Введите данные вверху окна:
```
Хост: [ваш FTP хост]
Имя пользователя: [ваш FTP логин]
Пароль: [ваш FTP пароль]
Порт: 21
```

### 3.3. Нажмите "Быстрое соединение"
✅ Должно подключиться и показать папки сервера справа

---

## ШАГ 4: Загрузка файлов (10-15 минут)

### 4.1. Найдите папку сайта на сервере
В правой части FileZilla (сервер) найдите и откройте:
- `public_html` 
- или `www`
- или папку с названием вашего домена

### 4.2. Откройте папку проекта на компьютере
В левой части FileZilla (ваш компьютер):
- Найдите папку `curs` на рабочем столе
- Откройте её

### 4.3. Загрузите ВСЕ файлы и папки
- Выделите ВСЕ файлы и папки (Ctrl+A)
- Перетащите из левой части в правую
- ⏳ Подождите завершения загрузки

**ВАЖНО:** Загружайте ВСЮ структуру проекта (все папки: app, public, config и т.д.)

---

## ШАГ 5: Создание базы данных (5 минут)

### 5.1. В панели Spaceweb найдите "Базы данных MySQL"

### 5.2. Создайте новую базу данных
Нажмите "Создать" или "Добавить"

**Заполните:**
- Имя БД: `commercial_proposals` (или любое другое)
- Пользователь: создайте нового (например: `db_user`)
- Пароль: придумайте надёжный (запишите!)

### 5.3. Запишите данные:
```
Имя БД: ________________
Пользователь БД: ________________
Пароль БД: ________________
Хост БД: localhost (или что указано)
```

---

## ШАГ 6: Создание таблиц в БД (10 минут)

### 6.1. Откройте phpMyAdmin
В панели Spaceweb найдите "phpMyAdmin" и откройте

### 6.2. Войдите
Используйте данные из шага 5 (пользователь и пароль БД)

### 6.3. Выберите вашу базу данных
В левом меню нажмите на название вашей БД

### 6.4. Откройте вкладку "SQL"

### 6.5. Скопируйте и выполните SQL
Откройте файл `SQL_МИГРАЦИИ.sql` из проекта и скопируйте ВСЕ запросы в окно SQL

**Или выполните по одному:**

**Запрос 1:**
```sql
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('guest','user','admin') NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Запрос 2:**
```sql
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Запрос 3:**
```sql
CREATE TABLE `templates` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `body_html` longtext NOT NULL,
  `variables` json DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Запрос 4:**
```sql
CREATE TABLE `proposals` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `offer_number` varchar(255) DEFAULT NULL,
  `offer_date` date NOT NULL,
  `seller_info` text DEFAULT NULL,
  `buyer_info` text DEFAULT NULL,
  `body_html` longtext NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT '₽',
  `vat_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `terms` text DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Запрос 5:**
```sql
CREATE TABLE `proposal_items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `proposal_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `unit` varchar(20) NOT NULL DEFAULT 'шт.',
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Запрос 6:**
```sql
CREATE TABLE `proposal_files` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `proposal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('logo','image') NOT NULL DEFAULT 'image',
  `original_name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Запрос 7:**
```sql
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Запрос 8 (создание администратора):**
```sql
INSERT INTO users (name, email, password, role, created_at, updated_at) 
VALUES (
    'Администратор', 
    'admin@example.com',  -- ЗАМЕНИТЕ НА СВОЙ EMAIL!
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'admin', 
    NOW(), 
    NOW()
);
```
**Пароль по умолчанию:** `password` (смените после входа!)

---

## ШАГ 7: Создание файла .env (5 минут)

### 7.1. Через FileZilla или файловый менеджер Spaceweb
Найдите папку с вашим сайтом (где загрузили файлы)

### 7.2. Создайте файл `.env`
- Правой кнопкой → "Создать файл"
- Имя: `.env`

### 7.3. Откройте файл и вставьте:
```env
APP_NAME="Commercial Proposals"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ваш-домен.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=commercial_proposals
DB_USERNAME=ваш_пользователь_бд
DB_PASSWORD=ваш_пароль_бд

FILESYSTEM_DRIVER=local
SESSION_DRIVER=file
```

**ЗАМЕНИТЕ:**
- `ваш-домен.com` → ваш реальный домен
- `commercial_proposals` → имя вашей БД (из шага 5)
- `ваш_пользователь_бд` → пользователь БД (из шага 5)
- `ваш_пароль_бд` → пароль БД (из шага 5)

### 7.4. Сохраните файл

---

## ШАГ 8: Настройка веб-сервера (ВАЖНО!)

### Вариант А: Если корень сайта = папка public_html

Нужно настроить, чтобы точка входа была `public/index.php`

**Через файловый менеджер Spaceweb:**
1. Найдите файл `.htaccess` в корне (или создайте)
2. Откройте и вставьте:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Перенаправление всех запросов в public/
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### Вариант Б: Если можно указать корневую директорию

В настройках домена в панели Spaceweb укажите корневую директорию на папку `public`

---

## ШАГ 9: Установка зависимостей (Composer)

### Если есть SSH доступ:
```bash
cd public_html
composer install --no-dev
```

### Если нет SSH:
1. Установите Composer на свой компьютер
2. Откройте терминал в папке проекта
3. Выполните: `composer install --no-dev`
4. Загрузите папку `vendor` на сервер через FileZilla

---

## ШАГ 10: Настройка прав доступа (2 минуты)

Через файловый менеджер Spaceweb:

1. Найдите папку `storage`
   - Правой кнопкой → "Права доступа" или "Chmod"
   - Установите: `755`

2. Найдите папку `bootstrap/cache`
   - Правой кнопкой → "Права доступа"
   - Установите: `755`

---

## ШАГ 11: Настройка PHP версии (2 минуты)

В панели Spaceweb:
1. Найдите "Настройки PHP" или "Версии PHP"
2. Выберите ваш домен
3. Установите PHP версию: **8.1** или выше
4. Сохраните

---

## ШАГ 12: Проверка работы (2 минуты)

### 12.1. Откройте сайт в браузере
Введите: `https://ваш-домен.com`

### 12.2. Должна открыться страница входа

### 12.3. Войдите:
- Email: тот, что указали в SQL запросе (шаг 6.5)
- Пароль: `password`

✅ **Если вошли - всё работает!**

---

## 🆘 Если не работает:

### Ошибка 500:
1. Проверьте `.env` - все данные правильные?
2. Проверьте права на папки `storage` и `bootstrap/cache` (755)
3. Включите отладку: в `.env` измените `APP_DEBUG=true` - увидите ошибку

### База данных не подключается:
1. Проверьте данные в `.env`
2. Убедитесь, что БД создана
3. Проверьте хост (может быть не `localhost`, а IP)

### Страница белая:
1. Включите `APP_DEBUG=true` в `.env`
2. Обновите страницу - увидите ошибку
3. Исправьте ошибку

---

## ✅ Чеклист:

- [ ] Файлы загружены через FileZilla
- [ ] База данных создана
- [ ] Таблицы созданы (SQL выполнены)
- [ ] Файл `.env` создан и заполнен
- [ ] Настроен `.htaccess` или корневая директория
- [ ] Зависимости установлены (папка `vendor`)
- [ ] Права на папки установлены (755)
- [ ] PHP версия 8.1+
- [ ] Администратор создан
- [ ] Сайт открывается
- [ ] Можно войти

---

**Готово! 🎉**

Если что-то не понятно - откройте файл `ИНСТРУКЦИЯ_ДЛЯ_ЧАЙНИКОВ.md` - там всё подробно расписано.


