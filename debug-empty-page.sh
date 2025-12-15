#!/bin/bash

# Диагностика пустой страницы

echo "========================================="
echo "Диагностика проблемы"
echo "========================================="

# 1. Проверка NGINX
echo ""
echo "=== 1. Статус NGINX ==="
systemctl status nginx --no-pager | head -10

# 2. Проверка PHP-FPM
echo ""
echo "=== 2. Статус PHP-FPM ==="
systemctl status php*-fpm --no-pager 2>/dev/null | head -10 || echo "PHP-FPM не найден"

# 3. Проверка сокетов PHP-FPM
echo ""
echo "=== 3. Сокеты PHP-FPM ==="
ls -la /var/run/php/*.sock 2>/dev/null || echo "Сокеты не найдены"

# 4. Проверка конфигурации NGINX
echo ""
echo "=== 4. Конфигурация NGINX ==="
cat /etc/nginx/sites-enabled/default | grep -A 2 "fastcgi_pass\|root"

# 5. Тест PHP
echo ""
echo "=== 5. Тест PHP ==="
cd /var/www/html/public
php -r "echo 'PHP работает: ' . PHP_VERSION . PHP_EOL;"

# 6. Тест выполнения index.php
echo ""
echo "=== 6. Тест выполнения index.php ==="
php index.php 2>&1 | head -20

# 7. Проверка логов NGINX
echo ""
echo "=== 7. Последние ошибки NGINX ==="
tail -30 /var/log/nginx/error.log

# 8. Проверка access логов
echo ""
echo "=== 8. Последние запросы ==="
tail -10 /var/log/nginx/access.log 2>/dev/null || echo "Access лог не найден"

# 9. Тест через curl
echo ""
echo "=== 9. Тест через curl ==="
curl -I http://localhost/ 2>&1 | head -10

# 10. Проверка прав доступа
echo ""
echo "=== 10. Права доступа ==="
ls -la /var/www/html/public/index.php
ls -la /var/www/html/bootstrap/app.php

