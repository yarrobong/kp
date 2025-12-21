<?php

namespace App\Support;

class Hash
{
    public static function make($value)
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }

    public static function check($value, $hashedValue)
    {
        return password_verify($value, $hashedValue);
    }
}



