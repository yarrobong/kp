#!/bin/bash

# Исправление 500 ошибки - замена на простую рабочую версию

cd /var/www/html

echo "========================================="
echo "Исправление 500 ошибки"
echo "========================================="

echo "Создание резервной копии..."
cp public/index.php public/index.php.backup

echo "Замена на простую рабочую версию..."
cp public/index-simple.php public/index.php

echo "Проверка синтаксиса..."
php -l public/index.php

echo "Тест работы..."
curl -s http://localhost/ | head -10

echo ""
echo "========================================="
echo "✅ Исправлено!"
echo "========================================="
echo ""
echo "Проверьте сайт: http://178.209.127.17"
echo ""
echo "Учетные данные:"
echo "Email: admin@example.com"
echo "Пароль: password"
echo ""
echo "Для отладки: http://178.209.127.17/debug"
echo ""
echo "Чтобы вернуть оригинальную версию:"
echo "cp public/index.php.backup public/index.php"

