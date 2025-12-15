<?php

namespace App\Http\Controllers;

use App\Http\Response;

class Controller
{
    protected function view($template, $data = [])
    {
        extract($data);
        $file = __DIR__ . '/../../resources/views/' . str_replace('.', '/', $template) . '.php';
        if (file_exists($file)) {
            ob_start();
            include $file;
            $content = ob_get_clean();
            echo $content;
            return;
        }
        throw new \Exception("View {$template} not found");
    }

    protected function redirect($path)
    {
        header('Location: ' . $path);
        exit;
    }

    protected function json($data, $status = 200)
    {
        return Response::json($data, $status);
    }
}



