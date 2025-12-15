<?php

namespace Illuminate\Support\Facades;

class Storage
{
    public static function disk($disk = null)
    {
        return new static();
    }

    public function put($path, $contents)
    {
        $fullPath = storage_path('app/public/' . $path);
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return file_put_contents($fullPath, $contents) !== false;
    }

    public function delete($path)
    {
        $fullPath = storage_path('app/public/' . $path);
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    public function exists($path)
    {
        return file_exists(storage_path('app/public/' . $path));
    }
}



