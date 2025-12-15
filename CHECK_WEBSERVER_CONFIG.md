# Проверка конфигурации веб-сервера

## Проблема

Файл `public/webhook.php` существует, но веб-сервер возвращает "File not found". Нужно проверить конфигурацию веб-сервера.

## Диагностика

Выполните на сервере:

```bash
ssh root@178.209.127.17

# 1. Проверьте, какой веб-сервер используется
systemctl status nginx 2>/dev/null || systemctl status apache2 2>/dev/null

# 2. Проверьте конфигурацию nginx
if [ -f /etc/nginx/sites-enabled/default ]; then
    echo "=== NGINX CONFIG ==="
    cat /etc/nginx/sites-enabled/default | grep -A 5 "root\|location"
fi

# 3. Проверьте конфигурацию Apache
if [ -f /etc/apache2/sites-enabled/000-default.conf ]; then
    echo "=== APACHE CONFIG ==="
    grep -A 5 "DocumentRoot\|Directory" /etc/apache2/sites-enabled/000-default.conf
fi

# 4. Проверьте, работает ли index.php
curl http://178.209.127.17/
curl http://178.209.127.17/index.php

# 5. Проверьте прямой доступ к файлу
curl http://178.209.127.17/public/webhook.php
```

## Возможные решения

### Решение 1: Если DocumentRoot = /var/www/html/public

В этом случае URL должен быть просто `/webhook.php` (без `/public/`).

Проверьте:
```bash
curl http://178.209.127.17/webhook.php
```

Если это работает, обновите URL в GitHub webhook на: `http://178.209.127.17/webhook.php`

### Решение 2: Если DocumentRoot = /var/www/html

В этом случае файл должен быть в корне, а URL: `/public/webhook.php`

Проверьте:
```bash
curl http://178.209.127.17/public/webhook.php
```

### Решение 3: Проверить rewrite правила

Если есть `.htaccess` или rewrite правила, они могут блокировать доступ.

Проверьте:
```bash
cd /var/www/html
ls -la .htaccess
cat .htaccess 2>/dev/null || echo "No .htaccess"
```

### Решение 4: Создать файл в обоих местах

Создайте файл и в корне, и в public:

```bash
cd /var/www/html

# Скопируйте из public в корень
cp public/webhook.php webhook.php

# Или создайте симлинк
ln -s public/webhook.php webhook.php
```

## Быстрое решение: Проверить все варианты

Выполните на сервере:

```bash
ssh root@178.209.127.17
cd /var/www/html

# Проверьте все возможные URL
echo "Testing URLs:"
curl -s -o /dev/null -w "%{http_code}" http://178.209.127.17/webhook.php && echo " - /webhook.php"
curl -s -o /dev/null -w "%{http_code}" http://178.209.127.17/public/webhook.php && echo " - /public/webhook.php"
curl -s -o /dev/null -w "%{http_code}" http://178.209.127.17/index.php && echo " - /index.php"

# Проверьте конфигурацию
echo ""
echo "Web server config:"
if command -v nginx &> /dev/null; then
    echo "NGINX:"
    grep -r "root" /etc/nginx/sites-enabled/ 2>/dev/null | head -3
elif command -v apache2 &> /dev/null; then
    echo "APACHE:"
    grep -r "DocumentRoot" /etc/apache2/sites-enabled/ 2>/dev/null | head -3
fi
```

## Альтернатива: Использовать Cron вместо Webhook

Если webhook продолжает вызывать проблемы, используйте более простой способ:

```bash
ssh root@178.209.127.17
cd /tmp
wget https://raw.githubusercontent.com/yarrobong/kp/main/deploy-cron.sh
chmod +x deploy-cron.sh
sudo bash deploy-cron.sh /var/www/html root
```

Этот способ не требует настройки webhook и работает автоматически каждые 5 минут.

