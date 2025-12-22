#!/bin/bash

# Основной скрипт автоматической синхронизации
# Вызывается при каждом изменении в проекте
# Использование: ./auto_sync.sh [commit_message]

# Настройки
COMMIT_MESSAGE=${1:-"Автоматический коммит изменений $(date +'%Y-%m-%d %H:%M:%S')"}
DEPLOY_TO_PRODUCTION=${DEPLOY_TO_PRODUCTION:-false}
PRODUCTION_SERVER=${PRODUCTION_SERVER:-""}
PRODUCTION_USER=${PRODUCTION_USER:-"deploy"}
PRODUCTION_PATH=${PRODUCTION_PATH:-"/var/www/kp-generator"}

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m'

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

deploy() {
    echo -e "${PURPLE}[$(date +'%Y-%m-%d %H:%M:%S')] ДЕПЛОЙ: $1${NC}"
}

# Проверка наличия git_sync.sh
if [ ! -f "git_sync.sh" ]; then
    error "Файл git_sync.sh не найден"
    exit 1
fi

log "🚀 Начинаем автоматическую синхронизацию..."

# 1. Синхронизация с git
log "📝 Шаг 1: Синхронизация с git..."
if ! ./git_sync.sh "$COMMIT_MESSAGE"; then
    error "Не удалось выполнить синхронизацию с git"
    exit 1
fi

# 2. Деплой на production (если настроено)
if [ "$DEPLOY_TO_PRODUCTION" = "true" ] && [ ! -z "$PRODUCTION_SERVER" ]; then
    deploy "Шаг 2: Деплой на production сервер..."
    if ! ./deploy_production.sh "$PRODUCTION_SERVER" "$PRODUCTION_USER" "$PRODUCTION_PATH"; then
        error "Не удалось выполнить деплой на production"
        exit 1
    fi
else
    info "Деплой на production пропущен (не настроен или отключен)"
fi

# 3. Локальный деплой (оптимизация)
log "🔧 Шаг 3: Локальная оптимизация..."
if ! ./deploy.sh development; then
    warning "Не удалось выполнить локальную оптимизацию"
fi

log "🎉 Автоматическая синхронизация завершена успешно!"

# Уведомление (если доступно)
if command -v notify-send &> /dev/null; then
    notify-send "КП Генератор" "Синхронизация завершена успешно!" -i dialog-information
elif command -v osascript &> /dev/null; then
    osascript -e "display notification \"Синхронизация завершена успешно!\" with title \"КП Генератор\" subtitle \"Git + Deploy\""
fi

echo ""
echo "📊 Резюме:"
echo "   ✅ Git синхронизация: выполнена"
if [ "$DEPLOY_TO_PRODUCTION" = "true" ] && [ ! -z "$PRODUCTION_SERVER" ]; then
    echo "   ✅ Production деплой: выполнен на $PRODUCTION_SERVER"
else
    echo "   ⏭️  Production деплой: пропущен"
fi
echo "   ✅ Локальная оптимизация: выполнена"
echo ""
echo "📝 Коммит: $COMMIT_MESSAGE"
