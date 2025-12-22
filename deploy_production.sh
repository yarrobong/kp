#!/bin/bash

# Скрипт развертывания КП Генератора на production сервер
# Использование: ./deploy_production.sh [server_ip] [ssh_user] [remote_path]

SERVER_IP=${1:-"your-server-ip"}
SSH_USER=${2:-"deploy"}
REMOTE_PATH=${3:-"/var/www/kp-generator"}
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Функции логирования
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ОШИБКА: $1${NC}"
}

warning() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] ВНИМАНИЕ: $1${NC}"
}

info() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')] ИНФО: $1${NC}"
}

log "🚀 Начинаем деплой на production сервер $SERVER_IP"

# Проверка наличия SSH доступа
log "🔍 Проверка SSH подключения..."
if ! ssh -o BatchMode=yes -o ConnectTimeout=5 "$SSH_USER@$SERVER_IP" "echo 'SSH OK'" > /dev/null 2>&1; then
    error "Не удалось подключиться к серверу $SERVER_IP по SSH"
    error "Убедитесь, что:"
    error "1. SSH ключ настроен для пользователя $SSH_USER"
    error "2. Сервер $SERVER_IP доступен"
    error "3. Пользователь $SSH_USER имеет права на $REMOTE_PATH"
    exit 1
fi

# Создание архива для отправки
log "📦 Создание архива проекта..."
ARCHIVE_NAME="kp-deploy-$(date +'%Y%m%d-%H%M%S').tar.gz"

# Исключаем ненужные файлы
tar -czf "$ARCHIVE_NAME" \
    --exclude='.git' \
    --exclude='node_modules' \
    --exclude='.DS_Store' \
    --exclude='*.log' \
    --exclude='temp/*' \
    --exclude='uploads/*' \
    --exclude='deploy_production.sh' \
    --exclude='git_sync.sh' \
    .

if [ ! -f "$ARCHIVE_NAME" ]; then
    error "Не удалось создать архив"
    exit 1
fi

log "📤 Отправка архива на сервер..."
if ! scp "$ARCHIVE_NAME" "$SSH_USER@$SERVER_IP:/tmp/"; then
    error "Не удалось отправить архив на сервер"
    rm -f "$ARCHIVE_NAME"
    exit 1
fi

# Выполнение команд на сервере
log "⚙️  Выполнение деплоя на сервере..."
ssh "$SSH_USER@$SERVER_IP" << EOF
    set -e

    echo "📦 Распаковка архива..."
    cd /tmp
    tar -xzf "$ARCHIVE_NAME"
    rm "$ARCHIVE_NAME"

    echo "📁 Создание резервной копии..."
    if [ -d "$REMOTE_PATH" ]; then
        BACKUP_NAME="backup-$(date +'%Y%m%d-%H%M%S')"
        mv "$REMOTE_PATH" "$REMOTE_PATH.$BACKUP_NAME"
        echo "✅ Старая версия сохранена как: $REMOTE_PATH.$BACKUP_NAME"
    fi

    echo "🚀 Установка новой версии..."
    mv kp-deploy-* "$REMOTE_PATH"

    cd "$REMOTE_PATH"

    echo "📦 Установка зависимостей..."
    if command -v composer &> /dev/null; then
        composer install --no-dev --optimize-autoloader
    else
        echo "⚠️  Composer не найден, пропускаем установку зависимостей"
    fi

    echo "📁 Создание директорий..."
    mkdir -p uploads/products
    mkdir -p logs
    mkdir -p temp

    echo "🔒 Установка прав доступа..."
    find . -type f -name "*.php" -exec chmod 644 {} \;
    find . -type f -name "*.sh" -exec chmod +x {} \;
    chmod 755 .
    chmod -R 755 uploads logs temp

    echo "⚙️  Оптимизация..."
    if [ -f "artisan" ]; then
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
    fi

    echo "🔍 Проверка синтаксиса..."
    if command -v php &> /dev/null; then
        SYNTAX_ERRORS=\$(find . -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors detected" || true)
        if [ ! -z "\$SYNTAX_ERRORS" ]; then
            echo "❌ Найдены ошибки синтаксиса PHP:"
            echo "\$SYNTAX_ERRORS"
            exit 1
        fi
    fi

    echo "✅ Деплой завершен успешно!"
EOF

DEPLOY_RESULT=$?

# Очистка локального архива
rm -f "$ARCHIVE_NAME"

if [ $DEPLOY_RESULT -eq 0 ]; then
    log "🎉 Деплой на сервер завершен успешно!"

    # Проверка доступности сайта
    log "🌐 Проверка доступности сайта..."
    if command -v curl &> /dev/null; then
        if curl -s --max-time 10 "http://$SERVER_IP/health" > /dev/null; then
            log "✅ Сайт доступен: http://$SERVER_IP"
        else
            warning "⚠️  Сайт недоступен для проверки, но деплой завершен"
        fi
    fi

    echo ""
    echo "📋 Информация о развертывании:"
    echo "   🌍 Сервер: $SERVER_IP"
    echo "   👤 Пользователь: $SSH_USER"
    echo "   📁 Путь: $REMOTE_PATH"
    echo "   📅 Время: $(date)"
    echo ""
    echo "🔍 Мониторинг: http://$SERVER_IP/health"
    echo "📚 Логи: ssh $SSH_USER@$SERVER_IP 'tail -f $REMOTE_PATH/logs/*.log'"

else
    error "Деплой завершился с ошибкой"
    exit 1
fi
