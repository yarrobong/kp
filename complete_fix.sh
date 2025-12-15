#!/bin/bash

# Полное исправление проекта на сервере

echo "🔍 Проверка и исправление проекта..."

cd /var/www/commercial_proposals || exit 1

# 1. Проверка структуры проекта
echo "📁 Проверка структуры проекта..."
if [ ! -d "app" ]; then
    echo "❌ Директория app не найдена!"
    exit 1
fi
if [ ! -d "public" ]; then
    echo "❌ Директория public не найдена!"
    exit 1
fi
if [ ! -f "public/index.php" ]; then
    echo "❌ Файл public/index.php не найден!"
    exit 1
fi

# 2. Исправление namespace в Session.php
echo "🔧 Исправление Session.php..."
if [ -f "app/Support/Session.php" ]; then
    sed -i 's/namespace Illuminate\\Support\\Facades;/namespace App\\Support;/' app/Support/Session.php
    echo "✅ Session.php исправлен"
else
    echo "❌ app/Support/Session.php не найден!"
fi

# 3. Исправление namespace в Route.php
echo "🔧 Исправление Route.php..."
if [ -f "routes/Route.php" ]; then
    if ! grep -q "namespace routes;" routes/Route.php; then
        sed -i '1a namespace routes;' routes/Route.php
        echo "✅ Namespace добавлен в Route.php"
    else
        echo "✅ Namespace уже есть в Route.php"
    fi
else
    echo "❌ routes/Route.php не найден!"
fi

# 4. Исправление routes/web.php
echo "🔧 Исправление routes/web.php..."
if [ -f "routes/web.php" ]; then
    if ! grep -q "use routes\\Route;" routes/web.php; then
        sed -i '/require_once.*Route.php/a use routes\\Route;' routes/web.php
        echo "✅ use routes\\Route; добавлен в web.php"
    else
        echo "✅ use routes\\Route; уже есть в web.php"
    fi
else
    echo "❌ routes/web.php не найден!"
fi

# 5. Проверка и создание директорий
echo "📁 Проверка директорий..."
mkdir -p bootstrap/cache
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/app/public

# 6. Настройка прав
echo "🔐 Настройка прав..."
chown -R www-data:www-data .
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env 2>/dev/null || true

# 7. Проверка .env файла
echo "⚙️ Проверка .env..."
if [ ! -f ".env" ]; then
    echo "Создание .env файла..."
    cat > .env << 'EOF'
APP_NAME="Commercial Proposals"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://178.209.127.17

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=commercial_proposals
DB_USERNAME=cp_user
DB_PASSWORD=Ispector228!

SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DRIVER=local
EOF
    chmod 644 .env
    echo "✅ .env файл создан"
fi

# 8. Перегенерация autoload
echo "🔄 Перегенерация autoload..."
if command -v composer &> /dev/null; then
    composer dump-autoload --optimize --no-dev --quiet
    echo "✅ Autoload перегенерирован"
else
    echo "⚠️ Composer не найден, пропускаем"
fi

# 9. Тест классов
echo "🧪 Тест классов..."
php -r "
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

\$classes = [
    'App\Support\Session' => 'Session',
    'routes\Route' => 'Route',
    'App\Http\Request' => 'Request',
];

foreach (\$classes as \$class => \$name) {
    if (class_exists(\$class)) {
        echo '✅ ' . \$name . ' найден' . PHP_EOL;
    } else {
        echo '❌ ' . \$name . ' не найден' . PHP_EOL;
    }
}
"

# 10. Проверка синтаксиса
echo "📝 Проверка синтаксиса..."
php -l public/index.php
php -l routes/web.php
php -l routes/Route.php

# 11. Перезапуск сервисов
echo "🔄 Перезапуск сервисов..."
systemctl restart php8.1-fpm
systemctl restart nginx

# 12. Финальный тест
echo "🌐 Финальный тест..."
sleep 2
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://178.209.127.17)

if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ Сайт работает! HTTP Status: $HTTP_CODE"
elif [ "$HTTP_CODE" = "302" ] || [ "$HTTP_CODE" = "301" ]; then
    echo "✅ Сайт работает! HTTP Status: $HTTP_CODE (редирект)"
else
    echo "❌ Проблема! HTTP Status: $HTTP_CODE"
    echo "Проверьте логи: tail -20 /var/log/nginx/commercial_proposals_error.log"
fi

echo ""
echo "🎉 Проверка завершена!"

