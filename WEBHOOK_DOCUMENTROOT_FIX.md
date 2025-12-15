# Исправление: DocumentRoot указывает на public/

## Проблема

Файл `webhook.php` создан в `/var/www/html/webhook.php`, но веб-сервер возвращает "File not found". Это означает, что DocumentRoot веб-сервера указывает на `/var/www/html/public`, а не на `/var/www/html`.

## Решение

### Вариант 1: Переместить файл в public/ (РЕКОМЕНДУЕТСЯ)

Выполните на сервере:

```bash
ssh root@178.209.127.17
cd /var/www/html

# Скопируйте файл в public/
cp webhook.php public/webhook.php

# Или создайте напрямую в public/
cat > public/webhook.php << 'WEBHOOK_EOF'
<?php
/**
 * GitHub Webhook для автоматического деплоя
 */

// Логирование
$logFile = dirname(__DIR__) . '/storage/logs/webhook.log';
$log = function($message) use ($logFile) {
    $timestamp = date('Y-m-d H:i:s');
    @mkdir(dirname($logFile), 0755, true);
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
};

$log("Webhook called");

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $log("Method not allowed");
    exit('Method not allowed');
}

// Получаем payload
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

// Проверяем, что это push в main ветку
if (!isset($data['ref']) || $data['ref'] !== 'refs/heads/main') {
    http_response_code(200);
    $log("Not a push to main branch, ignoring");
    exit('Not a push to main branch');
}

$log("Push to main detected. Starting deployment...");

// Путь к проекту (на уровень выше public/)
$deployPath = dirname(__DIR__);
chdir($deployPath);

// Сохраняем .env
if (file_exists('.env')) {
    copy('.env', '.env.backup');
}

// Обновляем код
exec('git fetch origin 2>&1', $output, $return);
$log("Git fetch: " . implode("\n", $output));

exec('git reset --hard origin/main 2>&1', $output, $return);
$log("Git reset: " . implode("\n", $output));

// Восстанавливаем .env
if (file_exists('.env.backup')) {
    rename('.env.backup', '.env');
}

// Composer install если нужно
if (file_exists('composer.json')) {
    exec('composer install --no-dev --optimize-autoloader --no-interaction 2>&1', $output, $return);
    $log("Composer install: " . implode("\n", $output));
}

// Права доступа
exec('chmod -R 755 storage bootstrap/cache 2>&1');
exec('chmod 644 .env 2>&1');

$log("Deployment completed");

http_response_code(200);
echo json_encode(['status' => 'success', 'message' => 'Deployment started']);
WEBHOOK_EOF

chmod 644 public/webhook.php

# Проверьте
ls -la public/webhook.php
curl http://178.209.127.17/webhook.php
```

### Вариант 2: Обновить URL в GitHub

Если файл находится в `public/webhook.php`, обновите URL в GitHub:

1. Откройте: https://github.com/yarrobong/kp/settings/hooks
2. Найдите ваш webhook и нажмите "Edit"
3. Измените Payload URL на: `http://178.209.127.17/webhook.php`
   (Если DocumentRoot = public/, то `/webhook.php` будет указывать на `public/webhook.php`)

### Вариант 3: Проверить настройки веб-сервера

Проверьте, куда указывает DocumentRoot:

```bash
ssh root@178.209.127.17

# Для nginx
cat /etc/nginx/sites-enabled/* | grep root

# Для Apache
grep DocumentRoot /etc/apache2/sites-enabled/*
```

Если DocumentRoot = `/var/www/html/public`, то:
- Файл должен быть в `public/webhook.php`
- URL: `http://178.209.127.17/webhook.php`

Если DocumentRoot = `/var/www/html`, то:
- Файл должен быть в корне: `webhook.php`
- URL: `http://178.209.127.17/webhook.php`

## Быстрое решение

Выполните на сервере:

```bash
ssh root@178.209.127.17
cd /var/www/html

# Создайте файл в public/
cat > public/webhook.php << 'EOF'
<?php
$logFile = dirname(__DIR__) . '/storage/logs/webhook.log';
$log = function($m) use ($logFile) {
    @mkdir(dirname($logFile), 0755, true);
    file_put_contents($logFile, date('Y-m-d H:i:s') . " $m\n", FILE_APPEND);
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['ref']) || $data['ref'] !== 'refs/heads/main') {
    http_response_code(200);
    exit;
}

$log("Deployment started");
chdir(dirname(__DIR__));

if (file_exists('.env')) copy('.env', '.env.backup');
exec('git fetch origin 2>&1', $o);
exec('git reset --hard origin/main 2>&1', $o);
if (file_exists('.env.backup')) rename('.env.backup', '.env');
if (file_exists('composer.json')) {
    exec('composer install --no-dev --optimize-autoloader --no-interaction 2>&1');
}
exec('chmod -R 755 storage bootstrap/cache 2>&1');
exec('chmod 644 .env 2>&1');

$log("Deployment completed");
http_response_code(200);
echo json_encode(['status' => 'success']);
EOF

chmod 644 public/webhook.php
mkdir -p storage/logs
chmod 755 storage/logs

# Проверьте
curl http://178.209.127.17/webhook.php
```

Должно вернуть ошибку 405 (Method not allowed) - это нормально, значит файл найден!

## Проверка

После создания файла в `public/webhook.php`:

1. Проверьте доступность:
```bash
curl http://178.209.127.17/webhook.php
```

2. Обновите webhook в GitHub (если нужно):
   - URL должен быть: `http://178.209.127.17/webhook.php`

3. Проверьте логи:
```bash
tail -f /var/www/html/storage/logs/webhook.log
```

