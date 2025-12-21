#!/bin/bash

# Скрипт развертывания КП Генератора
# Использование: ./deploy.sh [environment]

ENVIRONMENT=${1:-production}
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "🚀 Начинаем развертывание КП Генератора ($ENVIRONMENT)"

# Проверка зависимостей
echo "📦 Проверка зависимостей..."
if ! command -v php &> /dev/null; then
    echo "❌ PHP не найден. Установите PHP 8.1+"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    echo "❌ Composer не найден. Установите Composer"
    exit 1
fi

# Установка зависимостей
echo "📦 Установка PHP зависимостей..."
composer install --no-dev --optimize-autoloader

# Создание необходимых директорий
echo "📁 Создание директорий..."
mkdir -p uploads/products
mkdir -p logs

# Установка прав доступа
echo "🔒 Установка прав доступа..."
chmod 755 .
chmod 644 *.php *.json *.md
chmod 644 public/css/*.css
chmod 755 uploads uploads/products

# Оптимизация для production
if [ "$ENVIRONMENT" = "production" ]; then
    echo "⚡ Оптимизация для production..."

    # Очистка кэша Composer
    composer dump-autoload --optimize

    # Проверка синтаксиса PHP
    echo "🔍 Проверка синтаксиса PHP..."
    find . -name "*.php" -exec php -l {} \; | grep -v "No syntax errors"

    # Создание .htaccess для Apache (опционально)
    if [ ! -f .htaccess ]; then
        cat > .htaccess << 'EOF'
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]
EOF
    fi
fi

# Проверка конфигурации
echo "🔧 Проверка конфигурации..."
php -r "
if (!file_exists('vendor/autoload.php')) {
    echo \"❌ Зависимости не установлены\n\";
    exit(1);
}
if (!is_writable('.')) {
    echo \"⚠️  Внимание: нет прав на запись в корневую директорию\n\";
}
echo \"✅ Конфигурация корректна\n\";
"

# Финальные инструкции
echo ""
echo "🎉 Развертывание завершено!"
echo ""
echo "📋 Следующие шаги:"
echo "1. Настройте веб-сервер (Nginx/Apache) на папку проекта"
echo "2. Убедитесь, что PHP 8.1+ доступен"
echo "3. Проверьте доступность сайта: http://your-domain.com"
echo "4. Проверьте состояние системы: http://your-domain.com/health"
echo ""
echo "📚 Документация: README.md"
echo "🔍 Мониторинг: /health endpoint"
echo ""
echo "✨ КП Генератор готов к использованию!"
