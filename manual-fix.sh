#!/bin/bash

echo "🔧 РУЧНОЕ ИСПРАВЛЕНИЕ ПРОБЛЕМ С СЕРВЕРОМ..."
echo "==========================================="

cd /var/www/html

# 1. Создаём недостающие директории
echo "1. Создаём директории..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/cache
mkdir -p storage/framework/views
mkdir -p storage/framework/sessions
mkdir -p storage/logs

# 2. Создаём .env файл
echo "2. Создаём .env файл..."
cat > .env << 'EOF'
APP_NAME="Commercial Proposals"
APP_ENV=production
APP_DEBUG=true
APP_URL=http://6236609-ga45246

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=commercial_proposals
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

LOG_CHANNEL=stack
EOF

# 3. Устанавливаем права доступа
echo "3. Устанавливаем права доступа..."
chown -R www-data:www-data .
chmod -R 755 storage bootstrap/cache 2>/dev/null || true
chmod 644 .env 2>/dev/null || true
find . -type f -name "*.php" -exec chmod 644 {} \; 2>/dev/null || true

# 4. Исправляем nginx конфигурацию
echo "4. Исправляем nginx конфигурацию..."
sudo tee /etc/nginx/sites-enabled/default > /dev/null << 'EOF'
server {
    listen 80;
    server_name 6236609-ga45246 178.209.127.17 _;

    root /var/www/html/public;
    index index.php index.html index.htm;

    charset utf-8;

    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
}
EOF

# 5. Перезапускаем сервисы
echo "5. Перезапускаем сервисы..."
sudo nginx -t
if [ $? -eq 0 ]; then
    echo "✓ Nginx конфигурация корректна"
    sudo systemctl reload nginx
    echo "✓ Nginx перезапущен"
else
    echo "✗ Ошибка в nginx конфигурации"
    exit 1
fi

sudo systemctl restart php8.1-fpm
echo "✓ PHP-FPM перезапущен"

# 6. Создаём простую рабочую версию
echo "6. Создаём тестовую версию..."
if [ -f "public/index.php" ]; then
    cp public/index.php public/index.php.backup
fi
cp public/index-simple.php public/index.php

echo ""
echo "====================================="
echo "✅ ВСЕ ИСПРАВЛЕНИЯ ПРИМЕНЕНЫ!"
echo ""
echo "🔍 Проверьте сайт:"
echo "   http://6236609-ga45246"
echo "   http://178.209.127.17"
echo ""
echo "📊 Для отладки:"
echo "   http://6236609-ga45246/debug"
echo ""
echo "🔙 Для восстановления полной версии:"
echo "   cp public/index.php.backup public/index.php"
