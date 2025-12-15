#!/bin/bash

# Скрипт для первоначальной настройки деплоя на сервере
# Запустите этот скрипт на сервере один раз для настройки git репозитория

set -e

DEPLOY_PATH="${1:-/var/www/html}"
REPO_URL="https://github.com/yarrobong/kp.git"

echo "Setting up deployment on server..."
echo "Deploy path: $DEPLOY_PATH"

# Создаем директорию если не существует
mkdir -p "$DEPLOY_PATH"
cd "$DEPLOY_PATH"

# Если git репозиторий уже существует, обновляем
if [ -d ".git" ]; then
    echo "Git repository already exists. Updating..."
    git fetch origin
    git reset --hard origin/main
else
    echo "Cloning repository..."
    git clone "$REPO_URL" .
fi

# Устанавливаем зависимости если composer доступен
if [ -f composer.json ] && command -v composer &> /dev/null; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Устанавливаем права доступа
echo "Setting permissions..."
chmod -R 755 storage bootstrap/cache 2>/dev/null || true
chmod 644 .env 2>/dev/null || true

# Создаем .env если не существует
if [ ! -f .env ]; then
    echo "Creating .env file from example..."
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        echo "APP_ENV=production" > .env
        echo "APP_DEBUG=false" >> .env
    fi
    echo "Please configure .env file with your database credentials!"
fi

echo "Setup completed!"
echo "Next steps:"
echo "1. Configure .env file with your database credentials"
echo "2. Run database migrations"
echo "3. Set up GitHub Secrets for automatic deployment"

