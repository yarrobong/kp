#!/bin/bash

echo "🔍 Проверка состояния сервера..."
echo "================================="

# Проверяем nginx
echo "1. Nginx статус:"
sudo systemctl status nginx --no-pager -l | head -10

echo ""
echo "2. PHP-FPM статус:"
sudo systemctl status php8.1-fpm --no-pager -l | head -10

echo ""
echo "3. Структура файлов:"
ls -la /var/www/html/
echo ""
ls -la /var/www/html/public/

echo ""
echo "4. Права доступа:"
ls -ld /var/www/html/storage
ls -ld /var/www/html/bootstrap/cache 2>/dev/null || echo "bootstrap/cache не существует"
ls -l /var/www/html/.env 2>/dev/null || echo ".env файл не найден"

echo ""
echo "5. Тест PHP:"
php -r "echo 'PHP работает: ' . PHP_VERSION . PHP_EOL;"

echo ""
echo "6. Тест nginx конфигурации:"
sudo nginx -t

echo ""
echo "7. Недавние логи nginx:"
sudo tail -10 /var/log/nginx/error.log 2>/dev/null || echo "Лог файл не найден"

echo ""
echo "8. Недавние логи PHP:"
sudo tail -10 /var/log/php8.1-fpm.log 2>/dev/null || echo "Лог файл не найден"

echo ""
echo "================================="
echo "✅ Проверка завершена!"
