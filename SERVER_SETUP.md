# Настройка проекта на сервере

## Проблема

На сервере нет git репозитория, поэтому файлы не синхронизируются с GitHub.

## Решение: Настройте проект на сервере

### Шаг 1: Клонируйте репозиторий на сервер

```bash
ssh root@178.209.127.17

# Перейдите в директорию сайта
cd /var/www/html

# Если директория не пустая, сделайте backup или очистите
# ВАЖНО: Сохраните .env файл если он есть!
if [ -f .env ]; then
    cp .env /root/.env.backup
fi

# Клонируйте репозиторий
git clone https://github.com/yarrobong/kp.git .

# Восстановите .env если был
if [ -f /root/.env.backup ]; then
    mv /root/.env.backup .env
fi
```

### Шаг 2: Установите зависимости

```bash
cd /var/www/html

# Установите Composer зависимости
composer install --no-dev --optimize-autoloader --no-interaction
```

### Шаг 3: Настройте права доступа

```bash
chmod -R 755 storage bootstrap/cache
chmod 644 .env
```

### Шаг 4: Проверьте наличие webhook.php

```bash
ls -la webhook.php
ls -la public/webhook.php
```

Оба файла должны существовать.

### Шаг 5: Создайте директорию для логов

```bash
mkdir -p storage/logs
chmod 755 storage/logs
```

## Быстрая установка (одной командой)

Выполните на сервере:

```bash
ssh root@178.209.127.17 << 'EOF'
cd /var/www/html

# Сохраняем .env если есть
if [ -f .env ]; then
    cp .env /root/.env.backup
fi

# Клонируем репозиторий
if [ -d .git ]; then
    echo "Git repository exists. Updating..."
    git pull origin main
else
    echo "Cloning repository..."
    git clone https://github.com/yarrobong/kp.git .
fi

# Восстанавливаем .env
if [ -f /root/.env.backup ]; then
    mv /root/.env.backup .env
fi

# Устанавливаем зависимости
if [ -f composer.json ] && command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Настраиваем права
chmod -R 755 storage bootstrap/cache
chmod 644 .env 2>/dev/null || true

# Создаем директорию для логов
mkdir -p storage/logs
chmod 755 storage/logs

echo "Setup completed!"
echo "Files:"
ls -la webhook.php
ls -la public/webhook.php
EOF
```

## Проверка

После выполнения команд проверьте:

```bash
# Проверьте git репозиторий
cd /var/www/html
git status

# Проверьте файлы
ls -la webhook.php
ls -la public/webhook.php

# Проверьте структуру
ls -la
```

## После настройки

1. Webhook будет работать по адресу: `http://178.209.127.17/webhook.php`
2. При каждом push в GitHub код будет автоматически обновляться на сервере
3. Логи будут в: `/var/www/html/storage/logs/webhook.log`

