#!/bin/bash

# Финальная проверка и обновление на сервере

echo "========================================="
echo "Финальная проверка и обновление"
echo "========================================="

# Обновляем код на сервере
echo "Обновление кода на сервере..."
ssh root@178.209.127.17 << 'EOF'
cd /var/www/html

# Обновляем код
git pull origin main

# Устанавливаем зависимости
if [ -f composer.json ] && command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Настраиваем права
chmod -R 755 storage bootstrap/cache
chmod 644 .env 2>/dev/null || true

# Проверяем PHP синтаксис
echo "Проверка синтаксиса PHP..."
php -l public/index.php
php -l bootstrap/app.php

# Тест выполнения
echo "Тест выполнения index.php..."
php public/index.php 2>&1 | head -5

echo ""
echo "✅ Обновление завершено!"
EOF

echo ""
echo "Откройте в браузере: http://178.209.127.17"

