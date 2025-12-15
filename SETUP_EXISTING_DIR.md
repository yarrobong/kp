# Настройка git в существующей директории

## Проблема

Директория `/var/www/html` не пустая, поэтому `git clone` не работает.

## Решение: Инициализируйте git в существующей директории

Выполните на сервере:

```bash
cd /var/www/html

# Сохраните важные файлы (если есть)
if [ -f .env ]; then
    cp .env /root/.env.backup
    echo ".env сохранен"
fi

# Инициализируйте git репозиторий
git init

# Добавьте remote
git remote add origin https://github.com/yarrobong/kp.git

# Получите код из GitHub
git fetch origin

# Переключитесь на main ветку
git checkout -b main origin/main

# Или если хотите перезаписать все файлы (ОСТОРОЖНО!)
# git reset --hard origin/main

# Восстановите .env если был
if [ -f /root/.env.backup ]; then
    mv /root/.env.backup .env
    echo ".env восстановлен"
fi

# Установите зависимости
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Настройте права
chmod -R 755 storage bootstrap/cache
chmod 644 .env 2>/dev/null || true
mkdir -p storage/logs
chmod 755 storage/logs

# Проверьте
ls -la webhook.php
git status
```

## Альтернатива: Очистить директорию и клонировать заново

Если можно удалить существующие файлы:

```bash
cd /var/www/html

# Сохраните только .env
if [ -f .env ]; then
    cp .env /root/.env.backup
fi

# Удалите все файлы кроме скрытых системных
rm -rf * .[^.]* 2>/dev/null || true

# Клонируйте репозиторий
git clone https://github.com/yarrobong/kp.git .

# Восстановите .env
if [ -f /root/.env.backup ]; then
    mv /root/.env.backup .env
fi

# Установите зависимости
composer install --no-dev --optimize-autoloader --no-interaction

# Настройте права
chmod -R 755 storage bootstrap/cache
chmod 644 .env
mkdir -p storage/logs
chmod 755 storage/logs
```

## Безопасный вариант: Создать backup и клонировать

```bash
cd /var/www/html

# Создайте backup
mkdir -p /root/html_backup_$(date +%Y%m%d)
cp -r * /root/html_backup_$(date +%Y%m%d)/ 2>/dev/null || true

# Сохраните .env отдельно
if [ -f .env ]; then
    cp .env /root/.env.backup
fi

# Переместите файлы в backup
mv * /root/html_backup_$(date +%Y%m%d)/ 2>/dev/null || true
mv .[^.]* /root/html_backup_$(date +%Y%m%d)/ 2>/dev/null || true

# Теперь клонируйте
git clone https://github.com/yarrobong/kp.git .

# Восстановите .env
if [ -f /root/.env.backup ]; then
    mv /root/.env.backup .env
fi

# Установите зависимости
composer install --no-dev --optimize-autoloader --no-interaction

# Настройте права
chmod -R 755 storage bootstrap/cache
chmod 644 .env
mkdir -p storage/logs
chmod 755 storage/logs
```

