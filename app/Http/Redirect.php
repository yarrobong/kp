<?php

namespace App\Http;

class Redirect
{
    protected $url;
    protected $status = 302;

    public static function to($url, $status = 302)
    {
        $instance = new static();
        $instance->url = $url;
        $instance->status = $status;
        $instance->send();
        exit;
    }

    public static function back($status = 302)
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return static::to($referer, $status);
    }

    public function with($key, $value)
    {
        session($key, $value);
        return $this;
    }

    public function withErrors($errors)
    {
        session('errors', $errors);
        return $this;
    }

    public function withInput()
    {
        session('_old_input', $_POST);
        return $this;
    }

    protected function send()
    {
        header('Location: ' . $this->url, true, $this->status);
    }
}
