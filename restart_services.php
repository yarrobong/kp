<?php

// Скрипт для перезапуска служб
echo "Попытка перезапуска служб...\n";

// Попытка перезапуска PHP-FPM
echo "Перезапуск PHP-FPM...\n";
exec('sudo systemctl restart php8.1-fpm 2>&1', $output, $return_var);
echo "Результат: " . ($return_var === 0 ? 'УСПЕХ' : 'ОШИБКА') . "\n";
if (!empty($output)) {
    echo "Вывод: " . implode("\n", $output) . "\n";
}

// Попытка перезапуска nginx
echo "Перезапуск nginx...\n";
exec('sudo systemctl reload nginx 2>&1', $output, $return_var);
echo "Результат: " . ($return_var === 0 ? 'УСПЕХ' : 'ОШИБКА') . "\n";
if (!empty($output)) {
    echo "Вывод: " . implode("\n", $output) . "\n";
}

// Проверка статуса служб
echo "Проверка статуса PHP-FPM...\n";
exec('sudo systemctl status php8.1-fpm --no-pager -l 2>&1', $output, $return_var);
echo "Статус: " . ($return_var === 0 ? 'АКТИВЕН' : 'НЕАКТИВЕН') . "\n";

echo "Проверка статуса nginx...\n";
exec('sudo systemctl status nginx --no-pager -l 2>&1', $output, $return_var);
echo "Статус: " . ($return_var === 0 ? 'АКТИВЕН' : 'НЕАКТИВЕН') . "\n";

echo "Готово.\n";
?>
