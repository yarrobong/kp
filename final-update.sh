#!/bin/bash

# Финальное обновление кода на сервере

echo "========================================="
echo "Обновление проекта на сервере"
echo "========================================="

cd /var/www/html

echo "Текущий коммит:"
git log --oneline -1

echo ""
echo "Обновление кода..."
git pull origin main

echo ""
echo "Новый коммит:"
git log --oneline -1

echo ""
echo "Установка зависимостей..."
if [ -f composer.json ] && command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi

echo ""
echo "Настройка прав доступа..."
chmod -R 755 storage bootstrap/cache
chmod 644 .env 2>/dev/null || true

echo ""
echo "Перезапуск PHP-FPM..."
systemctl restart php8.1-fpm

echo ""
echo "Тест работы..."
php -l public/index.php
php -l bootstrap/app.php
php -l routes/web.php

echo ""
echo "Тест выполнения..."
php public/index.php 2>&1 | head -10

echo ""
echo "========================================="
echo "✅ Обновление завершено!"
echo "========================================="
echo ""
echo "Проверьте сайт: http://178.209.127.17"

