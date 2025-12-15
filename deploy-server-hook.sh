#!/bin/bash

# Скрипт для настройки автоматического деплоя через git hook на сервере
# Запустите этот скрипт на сервере один раз

set -e

DEPLOY_PATH="${1:-/var/www/html}"
REPO_URL="https://github.com/yarrobong/kp.git"

echo "Setting up automatic deployment on server..."
echo "Deploy path: $DEPLOY_PATH"

# Создаем директорию если не существует
mkdir -p "$DEPLOY_PATH"
cd "$DEPLOY_PATH"

# Если git репозиторий уже существует, обновляем
if [ -d ".git" ]; then
    echo "Git repository already exists."
    git remote set-url origin "$REPO_URL" 2>/dev/null || true
else
    echo "Cloning repository..."
    git clone "$REPO_URL" .
fi

# Создаем директорию для bare репозитория (для работы с hooks)
BARE_REPO="/var/repos/kp.git"
mkdir -p "$(dirname $BARE_REPO)"

if [ ! -d "$BARE_REPO" ]; then
    echo "Creating bare repository for hooks..."
    git clone --bare "$REPO_URL" "$BARE_REPO"
fi

# Создаем post-receive hook
HOOK_FILE="$BARE_REPO/hooks/post-receive"
cat > "$HOOK_FILE" << 'HOOK_SCRIPT'
#!/bin/bash

DEPLOY_PATH="/var/www/html"
cd "$DEPLOY_PATH" || exit

echo "========================================="
echo "Deployment started at $(date)"
echo "========================================="

# Получаем последние изменения
git fetch origin
git reset --hard origin/main

# Сохраняем .env если существует
if [ -f .env ]; then
    cp .env .env.backup
    git reset --hard origin/main
    if [ -f .env.backup ]; then
        mv .env.backup .env
    fi
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
HOOK_SCRIPT

chmod +x "$HOOK_FILE"

# Настраиваем remote в рабочем репозитории для работы с bare repo
cd "$DEPLOY_PATH"
git remote set-url origin "$BARE_REPO" 2>/dev/null || git remote add origin "$BARE_REPO"

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
echo "To deploy manually, run on server:"
echo "  cd $DEPLOY_PATH"
echo "  git pull origin main"
echo ""
echo "Or push to bare repo:"
echo "  git push origin main"
echo ""
echo "Next: Configure GitHub webhook (optional)"
echo "Webhook URL: http://your-server-ip/webhook.php"
echo "Or use the simpler cron method (see deploy-cron.sh)"

