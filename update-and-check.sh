#!/bin/bash

# Обновление и проверка файлов на сервере

echo "========================================="
echo "Обновление и проверка файлов"
echo "========================================="

ssh root@178.209.127.17 << 'EOF'
cd /var/www/html

echo "Текущий коммит до обновления:"
git log --oneline -1

echo ""
echo "Обновление кода..."
git pull origin main

echo ""
echo "Новый коммит:"
git log --oneline -1

echo ""
echo "Проверка файлов..."
ls -la public/index.php
ls -la public/server-info.php
ls -la public/index-simple.php

echo ""
echo "Тест работы..."
curl -s -I http://localhost/ | head -3

echo ""
echo "========================================="
echo "✅ Проверка завершена"
echo "========================================="
echo ""
echo "Информация о сервере: http://178.209.127.17/server-info.php"
echo "Отладка: http://178.209.127.17/debug"
echo "Главная: http://178.209.127.17"
EOF

