#!/bin/bash

# Настройка директорий кеша

cd /var/www/html

echo "Создание директорий кеша..."
mkdir -p bootstrap/cache
mkdir -p storage/cache
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/views
mkdir -p storage/framework/sessions

echo "Настройка прав доступа..."
chmod -R 755 storage bootstrap/cache
chmod 644 .env 2>/dev/null || true

echo "Директории созданы:"
ls -la bootstrap/cache
ls -la storage/cache
ls -la storage/logs
