<?php

// Информация о сервере и файлах

echo "<h1>Информация о сервере</h1>";
echo "<p><strong>Дата:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>PHP версия:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Текущая директория:</strong> " . getcwd() . "</p>";
echo "<hr>";

echo "<h2>Последние коммиты Git</h2>";
echo "<pre>";
exec('git log --oneline -5', $output);
echo implode("\n", $output);
echo "</pre><hr>";

echo "<h2>Статус Git</h2>";
echo "<pre>";
exec('git status --short', $output);
echo implode("\n", $output);
echo "</pre><hr>";

echo "<h2>Структура проекта</h2>";
echo "<pre>";
$files = scandir('.');
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $isDir = is_dir($file);
        $size = $isDir ? '' : filesize($file);
        $modTime = date('Y-m-d H:i:s', filemtime($file));
        echo sprintf("%s %10s %s %s\n",
            $isDir ? 'd' : '-',
            $size,
            $modTime,
            $file
        );
    }
}
echo "</pre><hr>";

echo "<h2>Файлы в public/</h2>";
echo "<pre>";
if (is_dir('public')) {
    $files = scandir('public');
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $path = 'public/' . $file;
            $isDir = is_dir($path);
            $size = $isDir ? '' : filesize($path);
            $modTime = date('Y-m-d H:i:s', filemtime($path));
            echo sprintf("%s %10s %s %s\n",
                $isDir ? 'd' : '-',
                $size,
                $modTime,
                $file
            );
        }
    }
} else {
    echo "Директория public не найдена!\n";
}
echo "</pre><hr>";

echo "<h2>Недавно измененные PHP файлы (последний час)</h2>";
echo "<pre>";
exec('find . -name "*.php" -type f -mmin -60 -exec ls -la {} \;', $output);
echo implode("\n", $output);
echo "</pre><hr>";

echo "<h2>Содержимое index.php</h2>";
echo "<pre>";
$content = file_get_contents('public/index.php');
echo htmlspecialchars(substr($content, 0, 500)) . "...";
echo "</pre><hr>";

echo "<h2>Тест HTTP запроса</h2>";
echo "<pre>";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Host: 178.209.127.17',
        'timeout' => 10
    ]
]);

$response = file_get_contents('http://localhost/', false, $context);
if ($response !== false) {
    $headers = $http_response_header ?? [];
    echo "HTTP Headers:\n";
    foreach ($headers as $header) {
        echo $header . "\n";
    }
    echo "\nFirst 200 chars of response:\n";
    echo htmlspecialchars(substr($response, 0, 200)) . "...";
} else {
    echo "Не удалось выполнить HTTP запрос\n";
}
echo "</pre><hr>";

echo "<h2>Логи ошибок NGINX (последние 10 строк)</h2>";
echo "<pre>";
exec('tail -10 /var/log/nginx/error.log', $output);
echo implode("\n", $output);
echo "</pre><hr>";

echo "<h2>Размер директории</h2>";
echo "<pre>";
exec('du -sh .', $output);
echo implode("\n", $output);
echo "</pre><hr>";

echo "<p><strong>Проверка завершена в:</strong> " . date('Y-m-d H:i:s') . "</p>";

