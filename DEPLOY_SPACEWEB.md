# Инструкция по развертыванию на Spaceweb

## Подготовка к загрузке

### 1. Подготовьте файлы для загрузки

Убедитесь, что у вас есть все необходимые файлы проекта. Структура должна быть следующей:

```
/
├── app/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── bootstrap/
├── composer.json
├── .htaccess
└── .env (создадите на сервере)
```

### 2. Создайте архив проекта

```bash
# Исключите ненужные файлы
zip -r project.zip . -x "*.git*" "node_modules/*" "vendor/*" ".env"
```

Или создайте архив вручную, исключив:
- `.git/`
- `node_modules/`
- `vendor/` (установите на сервере)
- `.env` (создадите на сервере)

## Загрузка на Spaceweb

### Способ 1: Через FTP (FileZilla или другой FTP-клиент)

1. **Получите данные FTP:**
   - Войдите в панель управления Spaceweb
   - Найдите раздел "FTP" или "Файлы"
   - Создайте FTP-пользователя (если нужно)
   - Запишите: хост, логин, пароль

2. **Подключитесь через FTP:**
   - Хост: `ftp.yourdomain.com` или IP адрес
   - Порт: 21
   - Логин: ваш FTP-логин
   - Пароль: ваш FTP-пароль

3. **Загрузите файлы:**
   - Перейдите в папку `public_html` или `www` (корневая директория сайта)
   - Загрузите все файлы проекта
   - **ВАЖНО:** Структура должна быть такой, чтобы `public/index.php` был доступен как точка входа

### Способ 2: Через панель управления (ISPmanager)

1. Войдите в панель ISPmanager
2. Перейдите в "Файловый менеджер"
3. Откройте папку вашего домена (обычно `public_html`)
4. Загрузите архив проекта
5. Распакуйте архив через файловый менеджер

### Способ 3: Через SSH (если доступен)

```bash
# Подключитесь к серверу
ssh username@yourdomain.com

# Перейдите в директорию сайта
cd public_html

# Загрузите файлы через scp (с локального компьютера)
scp -r /path/to/project/* username@yourdomain.com:~/public_html/
```

## Настройка на сервере

### 1. Создайте базу данных MySQL

1. В панели Spaceweb найдите раздел "Базы данных MySQL"
2. Создайте новую базу данных:
   - Имя БД: `commercial_proposals` (или другое)
   - Пользователь БД: создайте нового пользователя
   - Пароль: придумайте надежный пароль
3. Запишите данные:
   - Имя БД
   - Имя пользователя
   - Пароль
   - Хост (обычно `localhost`)

### 2. Выполните миграции БД

**Вариант А: Через phpMyAdmin**
1. Откройте phpMyAdmin в панели Spaceweb
2. Выберите созданную базу данных
3. Откройте вкладку "SQL"
4. Выполните SQL из миграций по порядку:
   - `database/migrations/2024_01_01_000001_create_users_table.php`
   - `database/migrations/2024_01_01_000002_create_templates_table.php`
   - `database/migrations/2024_01_01_000003_create_proposals_table.php`
   - `database/migrations/2024_01_01_000004_create_proposal_files_table.php`
   - `database/migrations/2024_01_01_000005_create_personal_access_tokens_table.php`

**Вариант Б: Через командную строку (SSH)**
```bash
mysql -u username -p database_name < migrations.sql
```

### 3. Создайте файл .env

В корне проекта создайте файл `.env` со следующим содержимым:

```env
APP_NAME="Commercial Proposals"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

FILESYSTEM_DRIVER=local
SESSION_DRIVER=file
```

**ВАЖНО:** Замените значения на ваши реальные данные!

### 4. Установите зависимости через Composer

Если на сервере установлен Composer:

```bash
# Через SSH
cd /path/to/your/site
composer install --no-dev --optimize-autoloader
```

Если Composer недоступен:
- Установите зависимости локально
- Загрузите папку `vendor/` на сервер

### 5. Настройте права доступа

```bash
# Через SSH или файловый менеджер установите права:
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

Или через файловый менеджер:
- `storage/` - права 755
- `bootstrap/cache/` - права 755
- `.env` - права 644

### 6. Создайте симлинк для storage (если нужно)

```bash
ln -s /path/to/storage/app/public /path/to/public/storage
```

Или создайте папку `public/storage` и настройте доступ к файлам.

## Настройка веб-сервера

### Вариант 1: Если корень сайта - папка public/

Если Spaceweb позволяет указать корневую директорию:
- Установите корневую директорию на `public/`
- Все запросы будут идти через `public/index.php`

### Вариант 2: Если корень - корень проекта

Если корень сайта - корень проекта, нужно настроить `.htaccess`:

Создайте или обновите `.htaccess` в корне проекта:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Перенаправление всех запросов в public/
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

Или переместите содержимое `public/` в корень сайта и обновите пути в `index.php`.

## Создание первого администратора

После установки создайте первого администратора через SQL:

```sql
INSERT INTO users (name, email, password, role, created_at, updated_at) 
VALUES (
    'Администратор', 
    'admin@yourdomain.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'admin', 
    NOW(), 
    NOW()
);
```

Пароль по умолчанию: `password` (смените после первого входа!)

Или используйте генератор хешей:
```php
<?php
echo password_hash('your_password', PASSWORD_BCRYPT);
?>
```

## Проверка работы

1. Откройте ваш сайт в браузере: `https://yourdomain.com`
2. Должна открыться страница входа или редирект на `/login`
3. Войдите с учетными данными администратора
4. Проверьте создание КП, загрузку файлов, экспорт PDF

## Возможные проблемы и решения

### Проблема: "500 Internal Server Error"
- Проверьте права доступа к файлам
- Проверьте логи ошибок в панели Spaceweb
- Убедитесь, что PHP версии 8.1+
- Проверьте `.htaccess` файл

### Проблема: "Database connection failed"
- Проверьте данные в `.env`
- Убедитесь, что БД создана и пользователь имеет права
- Проверьте хост БД (может быть не `localhost`, а IP)

### Проблема: "Class not found"
- Убедитесь, что установлены зависимости: `composer install`
- Проверьте автозагрузку классов

### Проблема: Файлы не загружаются
- Проверьте права на папку `storage/app/public`
- Убедитесь, что папка существует и доступна для записи

### Проблема: CSS/JS не загружаются
- Проверьте пути в шаблонах (должны начинаться с `/`)
- Убедитесь, что файлы загружены в `public/css/` и `public/js/`

## Дополнительные настройки Spaceweb

### Включите необходимые расширения PHP

В панели Spaceweb найдите настройки PHP и убедитесь, что включены:
- `pdo_mysql`
- `gd` (для работы с изображениями)
- `mbstring`
- `openssl`

### Настройте версию PHP

Убедитесь, что используется PHP 8.1 или выше:
- В панели Spaceweb найдите настройки PHP
- Выберите версию PHP 8.1+

### Настройте лимиты

Если нужно, увеличьте лимиты в `php.ini`:
- `upload_max_filesize = 10M`
- `post_max_size = 10M`
- `memory_limit = 256M`

## Безопасность

После развертывания:

1. **Убедитесь, что `.env` не доступен извне:**
   - Проверьте, что файл имеет права 644
   - Убедитесь, что веб-сервер не отдает этот файл

2. **Отключите отладку в продакшене:**
   ```env
   APP_DEBUG=false
   APP_ENV=production
   ```

3. **Используйте HTTPS:**
   - Включите SSL сертификат в панели Spaceweb
   - Обновите `APP_URL` на `https://`

4. **Смените пароли по умолчанию:**
   - Пароль администратора
   - Пароль БД
   - FTP пароли

## Поддержка

Если возникли проблемы:
1. Проверьте логи ошибок в панели Spaceweb
2. Обратитесь в техподдержку Spaceweb
3. Проверьте документацию Spaceweb по настройке PHP приложений



