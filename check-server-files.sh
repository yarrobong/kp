#!/bin/bash

# Проверка файлов на сервере

echo "========================================="
echo "Проверка файлов на сервере"
echo "========================================="

ssh root@178.209.127.17 << 'EOF'
cd /var/www/html

echo "Текущая директория: $(pwd)"
echo ""

echo "=== Информация о Git ==="
git log --oneline -3
echo ""
git status --short
echo ""

echo "=== Структура проекта ==="
ls -la | head -10
echo ""

echo "=== Файлы в public/ ==="
ls -la public/
echo ""

echo "=== Последние изменения файлов ==="
find . -name "*.php" -type f -mmin -60 -exec ls -la {} \; | head -10
echo ""

echo "=== Содержимое index.php ==="
head -10 public/index.php
echo "..."
echo ""

echo "=== Проверка работы ==="
curl -s -I http://localhost/ | head -5
echo ""

echo "=== Логи ошибок (последние 5 строк) ==="
tail -5 /var/log/nginx/error.log
echo ""

echo "=== Размер директории ==="
du -sh .
EOF

