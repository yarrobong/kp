#!/bin/bash

# Финальное исправление routes/web.php на сервере

echo "🔧 Исправление routes/web.php..."

cd /var/www/commercial_proposals

# Проверить текущее содержимое
echo "📝 Текущее содержимое routes/web.php (первые 10 строк):"
head -10 routes/web.php

# Удалить все неправильные use statements с routesRoute
echo "🧹 Очистка неправильных use statements..."
sed -i '/use routesRoute/d' routes/web.php
sed -i '/use routes\\Route/d' routes/web.php

# Добавить правильный use statement после require_once
echo "✏️ Добавление правильного use statement..."
sed -i '/require_once.*Route.php/a use routes\\Route;' routes/web.php

# Проверить результат
echo "✅ Исправленный routes/web.php (первые 10 строк):"
head -10 routes/web.php

# Проверить синтаксис
echo "📝 Проверка синтаксиса..."
php -l routes/web.php

# Перезапустить сервисы
echo "🔄 Перезапуск сервисов..."
systemctl restart php8.1-fpm
systemctl restart nginx

# Финальный тест
echo "🌐 Финальный тест..."
sleep 2
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://178.209.127.17)

if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ Сайт работает! HTTP Status: $HTTP_CODE"
elif [ "$HTTP_CODE" = "302" ] || [ "$HTTP_CODE" = "301" ]; then
    echo "✅ Сайт работает! HTTP Status: $HTTP_CODE (редирект)"
else
    echo "❌ Проблема! HTTP Status: $HTTP_CODE"
    echo "Проверьте логи: tail -20 /var/log/nginx/commercial_proposals_error.log"
fi

echo ""
echo "🎉 Исправление завершено!"

