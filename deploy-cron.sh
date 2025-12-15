#!/bin/bash

# Скрипт для настройки автоматического деплоя через cron на сервере
# Самый простой способ - проверяет GitHub каждые 5 минут и обновляет код

set -e

DEPLOY_PATH="${1:-/var/www/html}"
REPO_URL="https://github.com/yarrobong/kp.git"
CRON_USER="${2:-root}"

echo "Setting up automatic deployment via cron..."
echo "Deploy path: $DEPLOY_PATH"
echo "Cron user: $CRON_USER"

# Создаем директорию если не существует
mkdir -p "$DEPLOY_PATH"
cd "$DEPLOY_PATH"

# Если git репозиторий уже существует, обновляем
if [ -d ".git" ]; then
    echo "Git repository already exists. Updating remote..."
    git remote set-url origin "$REPO_URL" 2>/dev/null || git remote add origin "$REPO_URL"
else
    echo "Cloning repository..."
    git clone "$REPO_URL" .
fi

# Создаем скрипт деплоя
DEPLOY_SCRIPT="/usr/local/bin/deploy-kp.sh"
cat > "$DEPLOY_SCRIPT" << 'DEPLOY_SCRIPT_CONTENT'
#!/bin/bash

DEPLOY_PATH="/var/www/html"
cd "$DEPLOY_PATH" || exit

# Получаем последний коммит из GitHub
LATEST_REMOTE=$(git ls-remote origin main | cut -f1)
LATEST_LOCAL=$(git rev-parse HEAD)

# Если есть изменения, обновляем
if [ "$LATEST_REMOTE" != "$LATEST_LOCAL" ]; then
    echo "$(date): New changes detected. Deploying..."
    
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
        composer install --no-dev --optimize-autoloader --no-interaction || true
    fi
    
    # Устанавливаем права доступа
    chmod -R 755 storage bootstrap/cache 2>/dev/null || true
    chmod 644 .env 2>/dev/null || true
    
    # Очищаем кеш если есть
    rm -rf storage/framework/cache/* storage/framework/views/* 2>/dev/null || true
    
    echo "$(date): Deployment completed!"
else
    echo "$(date): No changes detected."
fi
DEPLOY_SCRIPT_CONTENT

chmod +x "$DEPLOY_SCRIPT"

# Создаем директорию для логов если не существует
mkdir -p /var/log
touch /var/log/deploy-kp.log
chmod 644 /var/log/deploy-kp.log

# Добавляем задачу в crontab
CRON_JOB="*/5 * * * * $DEPLOY_SCRIPT >> /var/log/deploy-kp.log 2>&1"

# Проверяем, не добавлена ли уже задача
if ! crontab -u "$CRON_USER" -l 2>/dev/null | grep -q "$DEPLOY_SCRIPT"; then
    echo "Adding cron job..."
    (crontab -u "$CRON_USER" -l 2>/dev/null; echo "$CRON_JOB") | crontab -u "$CRON_USER" -
    echo "Cron job added successfully!"
else
    echo "Cron job already exists."
fi

# Устанавливаем зависимости если нужно
if [ -f composer.json ] && command -v composer &> /dev/null; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction || true
fi

# Устанавливаем права доступа
chmod -R 755 storage bootstrap/cache 2>/dev/null || true
chmod 644 .env 2>/dev/null || true

echo ""
echo "========================================="
echo "Setup completed!"
echo "========================================="
echo ""
echo "Cron job will check for updates every 5 minutes"
echo "Logs: /var/log/deploy-kp.log"
echo ""
echo "To check cron job:"
echo "  crontab -u $CRON_USER -l"
echo ""
echo "To test deployment manually:"
echo "  $DEPLOY_SCRIPT"
echo ""
echo "To view logs:"
echo "  tail -f /var/log/deploy-kp.log"

