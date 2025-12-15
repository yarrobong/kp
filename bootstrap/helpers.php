<?php

if (!function_exists('session')) {
    function session($key = null, $value = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($key === null) {
            return $_SESSION ?? [];
        }
        
        // Если передан массив, устанавливаем все значения
        if (is_array($key) && $value === null) {
            foreach ($key as $k => $v) {
                $_SESSION[$k] = $v;
            }
            return;
        }
        
        if ($value === null) {
            return $_SESSION[$key] ?? null;
        }
        
        $_SESSION[$key] = $value;
    }
}

if (!function_exists('redirect')) {
    function redirect($path = null)
    {
        if ($path === null) {
            return new \App\Http\Redirect();
        }
        header('Location: ' . $path);
        exit;
    }
}

if (!function_exists('redirect_back')) {
    function redirect_back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $referer);
        exit;
    }
}

if (!function_exists('view')) {
    function view($template, $data = [])
    {
        extract($data);
        $file = __DIR__ . '/../resources/views/' . str_replace('.', '/', $template) . '.php';
        if (file_exists($file)) {
            ob_start();
            include $file;
            $content = ob_get_clean();
            echo $content;
            return;
        }
        throw new Exception("View {$template} not found");
    }
}

if (!function_exists('asset')) {
    function asset($path)
    {
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('abort')) {
    function abort($code, $message = '')
    {
        http_response_code($code);
        echo $message ?: "HTTP {$code}";
        exit;
    }
}

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('storage_path')) {
    function storage_path($path = '')
    {
        return __DIR__ . '/../storage' . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('config')) {
    function config($key, $default = null)
    {
        $keys = explode('.', $key);
        $file = __DIR__ . '/../config/' . array_shift($keys) . '.php';
        if (file_exists($file)) {
            $config = require $file;
            foreach ($keys as $k) {
                $config = $config[$k] ?? null;
            }
            return $config ?? $default;
        }
        return $default;
    }
}



