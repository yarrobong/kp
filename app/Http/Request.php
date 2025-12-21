<?php

namespace App\Http;

class Request
{
    public $attributes = [];
    public $request = [];
    public $query = [];
    public $server = [];
    public $files = [];
    public $cookies = [];
    public $headers = [];

    public function __construct()
    {
        $this->request = $_POST;
        $this->query = $_GET;
        $this->server = $_SERVER;
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;
        $this->headers = getallheaders() ?: [];
    }

    public static function capture()
    {
        return new static();
    }

    public function input($key = null, $default = null)
    {
        if ($key === null) {
            return array_merge($this->query, $this->request);
        }
        return $this->request[$key] ?? $this->query[$key] ?? $default;
    }

    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    public function post($key = null, $default = null)
    {
        if ($key === null) {
            return $this->request;
        }
        return $this->request[$key] ?? $default;
    }

    public function file($key)
    {
        return $this->files[$key] ?? null;
    }

    public function has($key)
    {
        return isset($this->request[$key]) || isset($this->query[$key]);
    }

    public function method()
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function isMethod($method)
    {
        return $this->method() === strtoupper($method);
    }

    public function expectsJson()
    {
        $accept = $this->header('Accept', '');
        return str_contains($accept, 'application/json');
    }

    public function header($key, $default = null)
    {
        $key = strtolower($key);
        return $this->headers[$key] ?? $default;
    }

    public function bearerToken()
    {
        $header = $this->header('Authorization', '');
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }
        return null;
    }

    public function is($pattern)
    {
        $path = parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        return fnmatch($pattern, $path);
    }

    public function path()
    {
        return parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    }

    public function url()
    {
        return ($this->server['HTTPS'] ?? 'off') === 'on' ? 'https' : 'http'
            . '://' . ($this->server['HTTP_HOST'] ?? 'localhost')
            . ($this->server['REQUEST_URI'] ?? '/');
    }

    public function setUserResolver(Closure $callback)
    {
        $this->attributes['user'] = $callback();
    }

    public function user()
    {
        return $this->attributes['user'] ?? null;
    }
}



