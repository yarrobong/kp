<?php

namespace App\Support;

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function get($key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function put($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function forget($key)
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function has($key)
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function flash($key, $value)
    {
        self::start();
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash($key, $default = null)
    {
        self::start();
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }
}



