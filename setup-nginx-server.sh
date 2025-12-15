#!/bin/bash

# Автоматическая настройка NGINX и PHP-FPM для проекта

set -e

echo "========================================="
echo "Настройка NGINX и PHP-FPM"
echo "========================================="

# Проверяем версию PHP
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;" 2>/dev/null || echo "8.1")
echo "Обнаружена версия PHP: $PHP_VERSION"

# Проверяем наличие PHP-FPM
if ! systemctl list-units --type=service | grep -q "php.*fpm"; then
    echo "Установка PHP-FPM..."
    apt-get update
    apt-get install -y php-fpm php-cli php-mysql php-mbstring php-xml php-curl php-zip
fi

# Находим правильный сокет PHP-FPM
PHP_FPM_SOCKET=""
if [ -S "/var/run/php/php${PHP_VERSION}-fpm.sock" ]; then
    PHP_FPM_SOCKET="/var/run/php/php${PHP_VERSION}-fpm.sock"
elif [ -S "/var/run/php/php8.1-fpm.sock" ]; then
    PHP_FPM_SOCKET="/var/run/php/php8.1-fpm.sock"
elif [ -S "/var/run/php/php8.2-fpm.sock" ]; then
    PHP_FPM_SOCKET="/var/run/php/php8.2-fpm.sock"
elif [ -S "/var/run/php/php8.3-fpm.sock" ]; then
    PHP_FPM_SOCKET="/var/run/php/php8.3-fpm.sock"
else
    # Пробуем найти любой PHP-FPM сокет
    PHP_FPM_SOCKET=$(ls /var/run/php/*.sock 2>/dev/null | head -1)
fi

if [ -z "$PHP_FPM_SOCKET" ]; then
    echo "Ошибка: PHP-FPM сокет не найден!"
    echo "Установка PHP-FPM..."
    apt-get update
    apt-get install -y php-fpm
    PHP_FPM_SOCKET="/var/run/php/php${PHP_VERSION}-fpm.sock"
fi

echo "Используется PHP-FPM сокет: $PHP_FPM_SOCKET"

# Создаем конфигурацию NGINX
echo "Создание конфигурации NGINX..."
cat > /etc/nginx/sites-enabled/default << NGINX_CONFIG
server {
    listen 80;
    listen [::]:80;
    
    server_name 178.209.127.17 _;
    root /var/www/html/public;
    index index.php index.html index.htm;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:$PHP_FPM_SOCKET;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX_CONFIG

# Проверяем конфигурацию NGINX
echo "Проверка конфигурации NGINX..."
if nginx -t; then
    echo "✅ Конфигурация NGINX корректна"
else
    echo "❌ Ошибка в конфигурации NGINX!"
    exit 1
fi

# Запускаем PHP-FPM если не запущен
echo "Запуск PHP-FPM..."
systemctl enable php${PHP_VERSION}-fpm 2>/dev/null || systemctl enable php-fpm 2>/dev/null || true
systemctl start php${PHP_VERSION}-fpm 2>/dev/null || systemctl start php-fpm 2>/dev/null || true

# Перезапускаем NGINX
echo "Перезапуск NGINX..."
systemctl restart nginx

# Проверяем статус
echo ""
echo "========================================="
echo "Проверка статуса сервисов"
echo "========================================="
systemctl status nginx --no-pager | head -5
echo ""
systemctl status php${PHP_VERSION}-fpm --no-pager 2>/dev/null | head -5 || systemctl status php-fpm --no-pager 2>/dev/null | head -5 || echo "PHP-FPM статус не доступен"

# Проверяем файлы проекта
echo ""
echo "========================================="
echo "Проверка файлов проекта"
echo "========================================="
cd /var/www/html
[ -f public/index.php ] && echo "✅ public/index.php существует" || echo "❌ public/index.php не найден"
[ -f bootstrap/app.php ] && echo "✅ bootstrap/app.php существует" || echo "❌ bootstrap/app.php не найден"
[ -f public/.htaccess ] && echo "✅ public/.htaccess существует" || echo "⚠️  public/.htaccess не найден (не критично для NGINX)"

# Настраиваем права доступа
echo ""
echo "Настройка прав доступа..."
chmod -R 755 storage bootstrap/cache 2>/dev/null || true
chmod 644 .env 2>/dev/null || true

echo ""
echo "========================================="
echo "✅ Настройка завершена!"
echo "========================================="
echo ""
echo "Откройте в браузере: http://178.209.127.17"
echo ""
echo "Если сайт не работает, проверьте логи:"
echo "  tail -20 /var/log/nginx/error.log"

