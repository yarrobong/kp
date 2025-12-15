#!/bin/bash

# Включение отображения ошибок PHP для отладки

echo "Включение отображения ошибок PHP..."

ssh root@178.209.127.17 << 'EOF'
cd /var/www/html

# Создаем php.ini для отладки в public/
cat > public/php-debug.ini << 'PHPINI'
display_errors = On
display_startup_errors = On
error_reporting = E_ALL
log_errors = On
error_log = /var/www/html/storage/logs/php-errors.log
PHPINI

# Или изменяем основной php.ini (если есть доступ)
if [ -f /etc/php/8.1/fpm/php.ini ]; then
    echo "Найден php.ini для PHP-FPM"
    # Делаем backup
    cp /etc/php/8.1/fpm/php.ini /etc/php/8.1/fpm/php.ini.backup
    
    # Включаем отображение ошибок
    sed -i 's/display_errors = Off/display_errors = On/' /etc/php/8.1/fpm/php.ini
    sed -i 's/display_startup_errors = Off/display_startup_errors = On/' /etc/php/8.1/fpm/php.ini
    sed -i 's/error_reporting = .*/error_reporting = E_ALL/' /etc/php/8.1/fpm/php.ini
    
    # Перезапускаем PHP-FPM
    systemctl restart php8.1-fpm
    echo "✅ PHP-FPM перезапущен с включенными ошибками"
fi

# Создаем директорию для логов если не существует
mkdir -p storage/logs
chmod 755 storage/logs

echo ""
echo "✅ Отображение ошибок включено!"
echo ""
echo "Проверьте:"
echo "1. http://178.209.127.17/debug.php - для отладки"
echo "2. tail -f /var/www/html/storage/logs/php-errors.log - для логов"
echo "3. tail -f /var/log/nginx/error.log - для логов NGINX"
EOF

