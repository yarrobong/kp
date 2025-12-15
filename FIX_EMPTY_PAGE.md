# Исправление пустой страницы на сервере

## Проблема

Сайт http://178.209.127.17 показывает пустую страницу.

## Диагностика на сервере

Выполните на сервере:

```bash
ssh root@178.209.127.17

# 1. Проверьте конфигурацию веб-сервера
echo "=== NGINX CONFIG ==="
grep -r "root" /etc/nginx/sites-enabled/ 2>/dev/null | head -5

echo ""
echo "=== APACHE CONFIG ==="
grep -r "DocumentRoot" /etc/apache2/sites-enabled/ 2>/dev/null | head -5

# 2. Проверьте наличие файлов
cd /var/www/html
echo ""
echo "=== СТРУКТУРА ПРОЕКТА ==="
ls -la public/index.php
ls -la .htaccess
ls -la public/.htaccess

# 3. Проверьте логи ошибок
echo ""
echo "=== ЛОГИ ОШИБОК ==="
tail -20 /var/log/nginx/error.log 2>/dev/null || tail -20 /var/log/apache2/error.log 2>/dev/null || echo "Логи не найдены"

# 4. Проверьте PHP ошибки
echo ""
echo "=== PHP ЛОГИ ==="
tail -20 /var/log/php*.log 2>/dev/null || echo "PHP логи не найдены"

# 5. Проверьте права доступа
echo ""
echo "=== ПРАВА ДОСТУПА ==="
ls -la public/index.php
ls -la bootstrap/app.php
```

## Решение 1: Проверить DocumentRoot

### Если используется NGINX:

DocumentRoot должен указывать на `/var/www/html/public`:

```bash
# Проверьте конфигурацию
cat /etc/nginx/sites-enabled/default | grep root

# Если root указывает на /var/www/html, нужно изменить на /var/www/html/public
# Отредактируйте файл:
nano /etc/nginx/sites-enabled/default

# Найдите строку:
# root /var/www/html;
# Измените на:
# root /var/www/html/public;

# Перезапустите nginx:
systemctl restart nginx
```

### Если используется Apache:

DocumentRoot должен указывать на `/var/www/html/public`:

```bash
# Проверьте конфигурацию
grep DocumentRoot /etc/apache2/sites-enabled/000-default.conf

# Если DocumentRoot указывает на /var/www/html, измените на /var/www/html/public
nano /etc/apache2/sites-enabled/000-default.conf

# Найдите строку:
# DocumentRoot /var/www/html
# Измените на:
# DocumentRoot /var/www/html/public

# Перезапустите Apache:
systemctl restart apache2
```

## Решение 2: Проверить .htaccess

Убедитесь, что `.htaccess` находится в `public/`:

```bash
cd /var/www/html

# Проверьте наличие .htaccess в public/
ls -la public/.htaccess

# Если файла нет, скопируйте из корня или создайте:
cp .htaccess public/.htaccess 2>/dev/null || cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF
```

## Решение 3: Проверить PHP ошибки

Включите отображение ошибок для диагностики:

```bash
cd /var/www/html/public

# Создайте тестовый файл
cat > test.php << 'EOF'
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "PHP работает!";
phpinfo();
EOF

# Откройте в браузере: http://178.209.127.17/test.php
```

## Решение 4: Проверить bootstrap/app.php

Убедитесь, что файл существует и доступен:

```bash
cd /var/www/html
ls -la bootstrap/app.php
cat bootstrap/app.php | head -20
```

## Быстрое решение: Проверить все одной командой

```bash
ssh root@178.209.127.17 << 'EOF'
cd /var/www/html

echo "=== 1. Проверка файлов ==="
[ -f public/index.php ] && echo "✅ public/index.php существует" || echo "❌ public/index.php не найден"
[ -f bootstrap/app.php ] && echo "✅ bootstrap/app.php существует" || echo "❌ bootstrap/app.php не найден"
[ -f public/.htaccess ] && echo "✅ public/.htaccess существует" || echo "❌ public/.htaccess не найден"

echo ""
echo "=== 2. Проверка конфигурации веб-сервера ==="
if command -v nginx &> /dev/null; then
    echo "NGINX:"
    grep "root" /etc/nginx/sites-enabled/* 2>/dev/null | head -3
elif command -v apache2 &> /dev/null; then
    echo "APACHE:"
    grep "DocumentRoot" /etc/apache2/sites-enabled/* 2>/dev/null | head -3
fi

echo ""
echo "=== 3. Тест PHP ==="
php -r "echo 'PHP работает: ' . PHP_VERSION . PHP_EOL;"
php public/index.php 2>&1 | head -10

echo ""
echo "=== 4. Права доступа ==="
ls -la public/index.php bootstrap/app.php
EOF
```

## После исправления

После изменения конфигурации веб-сервера:

1. Перезапустите веб-сервер:
```bash
systemctl restart nginx  # или apache2
```

2. Проверьте сайт: http://178.209.127.17

3. Должна открыться страница входа или редирект на `/login`

