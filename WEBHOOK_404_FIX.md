# Исправление ошибки "File not found" для webhook

## Проблема

GitHub отправляет webhook запрос, но сервер возвращает "File not found" (404). Это означает, что файл `webhook.php` не существует на сервере или находится не в том месте.

## Решение

### Шаг 1: Проверьте наличие файла на сервере

Выполните на сервере:

```bash
ssh root@178.209.127.17
cd /var/www/html

# Проверьте наличие файла
ls -la webhook.php
ls -la public/webhook.php

# Проверьте структуру директории
pwd
ls -la
```

### Шаг 2: Если файла нет - создайте его вручную

Если файла нет, создайте его:

```bash
cd /var/www/html

# Создайте файл webhook.php в корне
cat > webhook.php << 'EOF'
<?php
/**
 * GitHub Webhook для автоматического деплоя
 */

// Логирование
$logFile = __DIR__ . '/storage/logs/webhook.log';
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

// Путь к проекту
$deployPath = __DIR__;
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
EOF

# Сделайте файл исполняемым (для веб-сервера)
chmod 644 webhook.php

# Создайте директорию для логов
mkdir -p storage/logs
chmod 755 storage/logs

# Проверьте
ls -la webhook.php
```

### Шаг 3: Проверьте настройки веб-сервера

Проверьте, куда указывает DocumentRoot:

```bash
# Для nginx
cat /etc/nginx/sites-enabled/* | grep root

# Для Apache
grep DocumentRoot /etc/apache2/sites-enabled/*
```

Если DocumentRoot указывает на `/var/www/html/public`, то файл должен быть в `public/webhook.php` или URL должен быть `/public/webhook.php`.

### Шаг 4: Проверьте доступность файла через браузер

Попробуйте открыть в браузере:
- `http://178.209.127.17/webhook.php`
- `http://178.209.127.17/public/webhook.php`

Если один из них работает, используйте этот URL в настройках GitHub webhook.

### Шаг 5: Обновите URL в GitHub

Если файл находится в `public/webhook.php`, обновите URL в GitHub:
1. Откройте: https://github.com/yarrobong/kp/settings/hooks
2. Найдите ваш webhook и нажмите "Edit"
3. Измените Payload URL на: `http://178.209.127.17/public/webhook.php`
4. Сохраните

## Быстрое решение: Создать файл в обоих местах

Создайте файл и в корне, и в public:

```bash
cd /var/www/html

# Скопируйте файл из public в корень (если есть)
if [ -f public/webhook.php ]; then
    cp public/webhook.php webhook.php
fi

# Или создайте в корне (см. команды выше)
```

## Проверка работы

После создания файла:

1. Проверьте доступность:
```bash
curl -X POST http://178.209.127.17/webhook.php
```

2. Проверьте логи:
```bash
tail -f /var/www/html/storage/logs/webhook.log
```

3. Сделайте тестовый push в GitHub и проверьте логи

