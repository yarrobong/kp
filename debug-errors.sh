#!/bin/bash

# Скрипт для диагностики ошибок на сервере

echo "========================================="
echo "Диагностика ошибок на сервере"
echo "========================================="

ssh root@178.209.127.17 << 'EOF'
cd /var/www/html

echo "=== 1. Проверка PHP ошибок ==="
php -l public/index.php
php -l bootstrap/app.php
php -l routes/web.php
php -l routes/Route.php

echo ""
echo "=== 2. Тест выполнения index.php ==="
php public/index.php 2>&1 | head -30

echo ""
echo "=== 3. Проверка логов NGINX ==="
tail -30 /var/log/nginx/error.log

echo ""
echo "=== 4. Проверка PHP-FPM логов ==="
tail -30 /var/log/php8.1-fpm.log 2>/dev/null || echo "PHP-FPM лог не найден"

echo ""
echo "=== 5. Проверка прав доступа ==="
ls -la public/index.php
ls -la bootstrap/app.php
ls -la storage/

echo ""
echo "=== 6. Тест через curl ==="
curl -v http://localhost/ 2>&1 | head -40

echo ""
echo "=== 7. Проверка конфигурации NGINX ==="
nginx -t

echo ""
echo "=== 8. Статус сервисов ==="
systemctl status nginx --no-pager | head -5
systemctl status php8.1-fpm --no-pager | head -5 2>/dev/null || echo "PHP-FPM не запущен"
EOF

