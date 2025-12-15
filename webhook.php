<?php
/**
 * GitHub Webhook для автоматического деплоя
 * Размещен в корне проекта для прямого доступа
 * URL: http://178.209.127.17/webhook.php
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

