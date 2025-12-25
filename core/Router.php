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
        $method = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        if ($method === 'POST') {
            error_log("Router POST request: $requestUri");
        }

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

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri, $params)) {
                try {
                    $this->callHandler($route['handler'], $params);
                    return;
                } catch (\Exception $e) {
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

        // Определяем имя файла контроллера (без namespace)
        $controllerFileName = $controllerName;
        if (substr($controllerName, -10) !== 'Controller') {
            $controllerFileName .= 'Controller';
        }

        $controllerFile = __DIR__ . '/../controllers/' . $controllerFileName . '.php';

        // Определяем полное имя класса с namespace
        $controllerClass = 'Controllers\\' . $controllerFileName;

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
        
        try {
            // Убеждаемся, что Controller загружен
            $controllerBaseFile = __DIR__ . '/Controller.php';
            if (!class_exists('Core\\Controller') && file_exists($controllerBaseFile)) {
                require_once $controllerBaseFile;
            }
            
            // Используем ErrorController для рендеринга страницы 404
            $controllerFile = __DIR__ . '/../controllers/ErrorController.php';
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controller = new \Controllers\ErrorController();
                $controller->error404($message);
                exit;
            }
        } catch (\Exception $e) {
            error_log("Error rendering 404 page: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
        } catch (\Error $e) {
            error_log("Fatal error rendering 404 page: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
        }
        
        // Fallback на простой вывод
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>404 - Страница не найдена</title></head><body><h1>404 - ' . htmlspecialchars($message) . '</h1></body></html>';
        exit;
    }

    /**
     * Обработка 500 ошибки
     */
    private function error500($message = 'Внутренняя ошибка сервера') {
        http_response_code(500);
        
        try {
            // Убеждаемся, что Controller загружен
            $controllerBaseFile = __DIR__ . '/Controller.php';
            if (!class_exists('Core\\Controller') && file_exists($controllerBaseFile)) {
                require_once $controllerBaseFile;
            }
            
            // Используем ErrorController для рендеринга страницы 500
            $controllerFile = __DIR__ . '/../controllers/ErrorController.php';
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controller = new \Controllers\ErrorController();
                $controller->error500($message);
                exit;
            }
        } catch (\Exception $e) {
            error_log("Error rendering 500 page: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
        } catch (\Error $e) {
            error_log("Fatal error rendering 500 page: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
        }
        
        // Fallback на простой вывод
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>500 - Ошибка сервера</title></head><body><h1>500 - ' . htmlspecialchars($message) . '</h1></body></html>';
        exit;
    }
}
