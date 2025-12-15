# Исправление ошибки 404 для webhook

## Проблема

Webhook возвращает 404, потому что файл `webhook.php` находится в `public/webhook.php`, но URL должен указывать на правильный путь.

## Решение

### Вариант 1: Если корень сайта = корень проекта (public_html)

В этом случае файл должен быть доступен по пути `/public/webhook.php`

**Обновите webhook URL в GitHub:**
- Старый: `http://178.209.127.17/webhook.php`
- Новый: `http://178.209.127.17/public/webhook.php`

### Вариант 2: Если корень сайта = папка public/

В этом случае файл уже в правильном месте, но нужно проверить настройки.

**Проверьте настройки веб-сервера:**
- DocumentRoot должен указывать на `/var/www/html/public` (или ваш путь)

### Вариант 3: Переместить webhook.php в корень проекта

Если хотите использовать `/webhook.php` напрямую:

```bash
ssh root@178.209.127.17
cd /var/www/html

# Скопируйте файл в корень
cp public/webhook.php webhook.php

# Или создайте новый файл в корне
```

Создайте файл `webhook.php` в корне проекта:

```php
<?php
/**
 * GitHub Webhook для автоматического деплоя
 * Размещен в корне проекта для прямого доступа
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
$deployScript = "$deployPath/deploy-webhook.sh";

// Запускаем деплой в фоне
if (file_exists($deployScript)) {
    exec("bash $deployScript >> $logFile 2>&1 &");
    $log("Deployment script started");
} else {
    // Простой деплой напрямую
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
}

http_response_code(200);
echo json_encode(['status' => 'success', 'message' => 'Deployment started']);
```

## Быстрая проверка

### 1. Проверьте текущую структуру на сервере:

```bash
ssh root@178.209.127.17
cd /var/www/html
ls -la
ls -la public/
```

### 2. Проверьте, где находится DocumentRoot:

```bash
# Для Apache
grep DocumentRoot /etc/apache2/sites-enabled/*

# Или проверьте конфигурацию nginx
cat /etc/nginx/sites-enabled/*
```

### 3. Протестируйте доступность файла:

```bash
# Попробуйте открыть в браузере:
# http://178.209.127.17/public/webhook.php
# или
# http://178.209.127.17/webhook.php
```

## Рекомендация

**Используйте вариант 1** - обновите URL в GitHub на:
```
http://178.209.127.17/public/webhook.php
```

Это самый простой способ без изменения файлов на сервере.

## Альтернатива: Используйте Cron вместо Webhook

Если webhook вызывает проблемы, используйте более простой способ - Cron:

```bash
ssh root@178.209.127.17
cd /tmp
wget https://raw.githubusercontent.com/yarrobong/kp/main/deploy-cron.sh
chmod +x deploy-cron.sh
sudo bash deploy-cron.sh /var/www/html root
```

Этот способ не требует настройки webhook и работает автоматически каждые 5 минут.

