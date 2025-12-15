#!/bin/bash

# Скрипт деплоя для webhook
# Запускается автоматически при получении webhook от GitHub

set -e

DEPLOY_PATH="$(cd "$(dirname "$0")" && pwd)"
cd "$DEPLOY_PATH"

echo "========================================="
echo "Deployment started at $(date)"
echo "========================================="

# Сохраняем .env если существует
if [ -f .env ]; then
    cp .env .env.backup
fi

# Получаем последние изменения
git fetch origin
git reset --hard origin/main

# Восстанавливаем .env
if [ -f .env.backup ]; then
    mv .env.backup .env
fi

# Устанавливаем зависимости если нужно
if [ -f composer.json ] && command -v composer &> /dev/null; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction || true
fi

# Устанавливаем права доступа
echo "Setting permissions..."
chmod -R 755 storage bootstrap/cache 2>/dev/null || true
chmod 644 .env 2>/dev/null || true

# Очищаем кеш если есть
rm -rf storage/framework/cache/* storage/framework/views/* 2>/dev/null || true

echo "========================================="
echo "Deployment completed successfully!"
echo "========================================="

