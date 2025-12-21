<?php
namespace Core;

/**
 * Роутер для обработки HTTP запросов
 * Обрабатывает маршруты и вызывает соответствующие контроллеры
 */
class Router {
    private $routes = [];

    /**
     * Добавление маршрута
     */
    public function add($method, $path, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    /**
     * Добавление GET маршрута
     */
    public function get($path, $handler) {
        $this->add('GET', $path, $handler);
    }

    /**
     * Добавление POST маршрута
     */
    public function post($path, $handler) {
        $this->add('POST', $path, $handler);
    }

    /**
     * Запуск роутинга
     */
    public function run() {
        error_log("Router run: " . $_SERVER['REQUEST_METHOD'] . " " . ($_SERVER['REQUEST_URI'] ?? '/'));
        $method = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        // Извлекаем путь из URI
        if (strpos($requestUri, '?') !== false) {
            $uri = strstr($requestUri, '?', true);
        } else {
            $uri = $requestUri;
        }

        // Удаляем trailing slash, но сохраняем '/' как '/'
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        // Если URI пустой, устанавливаем корень
        if (!$uri || $uri === '') {
            $uri = '/';
        }

        error_log("Looking for route: $method $uri");
        error_log("Total routes: " . count($this->routes));

        foreach ($this->routes as $route) {
            error_log("Checking route: " . $route['method'] . " " . $route['path']);
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri, $params)) {
                error_log("Route matched: " . $route['path']);
                try {
                    $this->callHandler($route['handler'], $params);
                    return;
                } catch (Exception $e) {
                    error_log("Router error: " . $e->getMessage());
                    $this->error500($e->getMessage());
                    return;
                }
            }
        }

        // Если маршрут не найден
        $this->error404();
    }

    /**
     * Проверка соответствия пути маршруту
     */
    private function matchPath($routePath, $requestUri, &$params) {
        $params = [];

        error_log("Matching: '$routePath' vs '$requestUri'");

        // Преобразуем параметры маршрута {id} в регулярные выражения
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        error_log("Pattern: $pattern");

        if (preg_match($pattern, $requestUri, $matches)) {
            error_log("Match found!");
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }
            return true;
        }

        error_log("No match");
        return false;
    }

    /**
     * Вызов обработчика маршрута
     */
    private function callHandler($handler, $params = []) {
        if (is_callable($handler)) {
            call_user_func($handler, $params);
        } elseif (is_string($handler)) {
            $this->callController($handler, $params);
        }
    }

    /**
     * Вызов контроллера
     */
    private function callController($handler, $params) {
        list($controllerName, $method) = explode('@', $handler);

        // Если имя контроллера уже содержит "Controller", не добавляем суффикс
        if (substr($controllerName, -10) === 'Controller') {
            $controllerClass = 'Controllers\\' . $controllerName;
        } else {
            $controllerClass = 'Controllers\\' . $controllerName . 'Controller';
        }

        $controllerFile = __DIR__ . '/../controllers/' . str_replace('\\', '/', $controllerClass) . '.php';

        if (!file_exists($controllerFile)) {
            throw new \Exception("Controller file not found: $controllerFile");
        }

        require_once $controllerFile;

        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller class not found: $controllerClass");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            throw new \Exception("Controller method not found: $method");
        }

        call_user_func_array([$controller, $method], [$params]);
    }

    /**
     * Обработка 404 ошибки
     */
    private function error404($message = 'Страница не найдена') {
        http_response_code(404);
        echo '<h1>404 - ' . $message . '</h1>';
    }

    /**
     * Обработка 500 ошибки
     */
    private function error500($message = 'Внутренняя ошибка сервера') {
        http_response_code(500);
        echo '<h1>500 - ' . $message . '</h1>';
    }
}
