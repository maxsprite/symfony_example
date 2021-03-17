<?php

namespace App\Utils;

final class PasswordGenerator
{
    public static function generate(int $length = 8): string
    {
        $chars    = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $password = substr(str_shuffle($chars), 0, $length);

        return $password;
    }

    public static function generateConfirmationCode()
    {
        return mt_rand(100000, 999999);
    }
}