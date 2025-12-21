<?php
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
        $method = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        // Извлекаем путь из URI
        if (strpos($requestUri, '?') !== false) {
            $uri = strstr($requestUri, '?', true);
        } else {
            $uri = $requestUri;
        }

        // Если URI пустой, устанавливаем корень
        if (!$uri || $uri === '') {
            $uri = '/';
        }

        // Удаляем trailing slash
        $uri = rtrim($uri, '/');

        // Временная отладка
        error_log("Router: Method=$method, URI='$uri', REQUEST_URI='$requestUri'");

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri, $params)) {
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

        // Преобразуем параметры маршрута {id} в регулярные выражения
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestUri, $matches)) {
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }
            return true;
        }

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
            $controllerClass = $controllerName;
        } else {
            $controllerClass = $controllerName . 'Controller';
        }

        $controllerFile = __DIR__ . '/../controllers/' . $controllerClass . '.php';

        if (!file_exists($controllerFile)) {
            throw new Exception("Controller file not found: $controllerFile");
        }

        require_once $controllerFile;

        if (!class_exists($controllerClass)) {
            throw new Exception("Controller class not found: $controllerClass");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            throw new Exception("Controller method not found: $method");
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
