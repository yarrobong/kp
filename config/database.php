<?php
/**
 * Конфигурация базы данных
 */
return [
    'host' => 'localhost',
    'database' => 'commercial_proposals',
    'username' => 'appuser',
    'password' => 'apppassword',
    'charset' => 'utf8',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ]
];
