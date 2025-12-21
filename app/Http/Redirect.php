<?php

namespace App\Http;

class Redirect
{
    protected $url;
    protected $sessionData = [];

    public function __construct($url = null)
    {
        $this->url = $url;
    }

    public static function to($url)
    {
        return new static($url);
    }

    public static function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return new static($referer);
    }

    public function with($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                session($k, $v);
            }
        } else {
            session($key, $value);
        }
        return $this;
    }

    public function withErrors($errors)
    {
        session('errors', $errors);
        return $this;
    }

    public function withInput($input = null)
    {
        if ($input === null) {
            $input = array_merge($_GET, $_POST);
        }
        session('_old_input', $input);
        return $this;
    }

    public function send()
    {
        header('Location: ' . $this->url);
        exit;
    }

    public function __toString()
    {
        $this->send();
        return '';
    }
}


