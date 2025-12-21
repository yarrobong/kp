<?php

class Route
{
    protected static $routes = [];
    protected static $middlewareGroups = [];

    public static function get($uri, $action)
    {
        return self::addRoute('GET', $uri, $action);
    }

    public static function post($uri, $action)
    {
        return self::addRoute('POST', $uri, $action);
    }

    public static function put($uri, $action)
    {
        return self::addRoute('PUT', $uri, $action);
    }

    public static function delete($uri, $action)
    {
        return self::addRoute('DELETE', $uri, $action);
    }

    public static function middleware($middleware)
    {
        return new RouteMiddleware($middleware);
    }

    public static function prefix($prefix)
    {
        return new RoutePrefix($prefix);
    }

    public static function group($callback, $options = [])
    {
        if (isset($options['middleware'])) {
            $middleware = is_array($options['middleware']) ? $options['middleware'] : [$options['middleware']];
            return new RouteMiddleware($middleware, $callback);
        }
        if (isset($options['prefix'])) {
            return new RoutePrefix($options['prefix'], $callback);
        }
        $callback();
    }

    protected static function addRoute($method, $uri, $action)
    {
        self::$routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action,
        ];
        return new static();
    }

    public static function dispatch()
    {
        $request = \App\Http\Request::capture();
        $method = $request->method();
        $path = $request->path();

        foreach (self::$routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = '#^' . preg_replace('/\{[^}]+\}/', '([^/]+)', $route['uri']) . '$#';
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);

                $action = $route['action'];
                if (is_array($action)) {
                    [$controller, $method] = $action;
                    $controller = new $controller();
                    return $controller->$method($request, ...$matches);
                } elseif (is_callable($action)) {
                    return $action($request, ...$matches);
                }
            }
        }

        http_response_code(404);
        echo '404 Not Found';
        exit;
    }
}

class RouteMiddleware
{
    protected $middleware;
    protected $callback;

    public function __construct($middleware, $callback = null)
    {
        $this->middleware = is_array($middleware) ? $middleware : [$middleware];
        $this->callback = $callback;
    }

    public function group($callback = null)
    {
        if ($callback) {
            $this->callback = $callback;
        }
        $this->execute();
    }

    protected function execute()
    {
        $kernel = new \App\Http\Kernel();
        $request = \App\Http\Request::capture();

        foreach ($this->middleware as $middlewareName) {
            $middlewareClass = $kernel->routeMiddleware[$middlewareName] ?? null;
            if ($middlewareClass) {
                $middleware = new $middlewareClass();
                $response = $middleware->handle($request, function($req) {
                    return null;
                });
                if ($response) {
                    $response->send();
                    exit;
                }
            }
        }

        if ($this->callback) {
            $this->callback();
        }
    }
}

class RoutePrefix
{
    protected $prefix;
    protected $callback;

    public function __construct($prefix, $callback = null)
    {
        $this->prefix = $prefix;
        $this->callback = $callback;
    }

    public function group($callback = null)
    {
        if ($callback) {
            $this->callback = $callback;
        }
        $this->execute();
    }

    protected function execute()
    {
        // В реальности нужно модифицировать URI перед маршрутизацией
        if ($this->callback) {
            $this->callback();
        }
    }
}



