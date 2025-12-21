<?php
/**
 * Главная точка входа приложения
 * Перенаправляет на public/index.php
 */

// Определение константы для корневой директории
define('ROOT_DIR', __DIR__);

// Перенаправление на public/index.php
header('Location: /public/index.php');
                exit;