# Настройка NGINX для проекта

## Проблема

Файл `/etc/nginx/sites-enabled/default` пустой, поэтому сайт не работает.

## Решение: Создать конфигурацию NGINX

Выполните на сервере:

```bash
ssh root@178.209.127.17

# Создайте конфигурацию NGINX
cat > /etc/nginx/sites-enabled/default << 'NGINX_CONFIG'
server {
    listen 80;
    listen [::]:80;
    
    server_name 178.209.127.17;
    root /var/www/html/public;
    index index.php index.html index.htm;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX_CONFIG

# Проверьте конфигурацию
nginx -t

# Если проверка прошла успешно, перезапустите NGINX
systemctl restart nginx

# Проверьте статус
systemctl status nginx
```

## Если PHP-FPM использует другой сокет

Проверьте версию PHP и путь к сокету:

```bash
# Проверьте версию PHP
php -v

# Найдите сокет PHP-FPM
ls -la /var/run/php/

# Если сокет другой (например, php8.2-fpm.sock), измените в конфигурации:
nano /etc/nginx/sites-enabled/default
# Найдите строку: fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
# Измените на правильный путь
```

## Альтернативная конфигурация (если PHP-FPM не работает)

Если PHP-FPM не настроен, используйте встроенный PHP сервер или настройте PHP-FPM:

```bash
# Установите PHP-FPM если не установлен
apt-get update
apt-get install -y php-fpm

# Включите PHP-FPM
systemctl enable php8.1-fpm
systemctl start php8.1-fpm

# Проверьте статус
systemctl status php8.1-fpm
```

## Проверка работы

После настройки:

1. Проверьте конфигурацию: `nginx -t`
2. Перезапустите NGINX: `systemctl restart nginx`
3. Откройте в браузере: http://178.209.127.17
4. Должна открыться страница входа или редирект на `/login`

## Диагностика проблем

Если сайт все еще не работает:

```bash
# Проверьте логи NGINX
tail -20 /var/log/nginx/error.log

# Проверьте логи PHP-FPM
tail -20 /var/log/php8.1-fpm.log

# Проверьте, работает ли PHP-FPM
systemctl status php8.1-fpm

# Проверьте права доступа
ls -la /var/www/html/public/index.php
```

