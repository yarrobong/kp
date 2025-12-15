<?php

namespace App\Http;

class Response
{
    protected $content;
    protected $status;
    protected $headers;

    public function __construct($content = '', $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    public static function json($data, $status = 200, array $headers = [])
    {
        $headers['Content-Type'] = 'application/json';
        return new static(json_encode($data), $status, $headers);
    }

    public function send()
    {
        http_response_code($this->status);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        echo $this->content;
    }

    public function __toString()
    {
        return $this->content;
    }
}



