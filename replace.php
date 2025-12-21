<?php

// Скрипт для замены index.php на простую версию
$content = file_get_contents('https://raw.githubusercontent.com/yarrobong/kp/main/simple-index.php');

if ($content !== false) {
    $result = file_put_contents('index.php', $content);
    if ($result !== false) {
        echo 'index.php успешно заменен на простую версию без базы данных';
    } else {
        echo 'Ошибка при сохранении файла';
    }
} else {
    echo 'Не удалось скачать файл с GitHub';
}

?>
