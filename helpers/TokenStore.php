<?php

namespace helpers;

session_start();

class TokenStore
{

    public static function storeToken(string $token, array $user): void
    {
        $_SESSION['tokens'][$token] = $user;
    }


    public static function getUserByToken(string $token): ?array
    {
        return $_SESSION['tokens'][$token] ?? null;
    }


    public static function revokeToken(string $token): void
    {
        unset($_SESSION['tokens'][$token]);
    }


    public static function clear(): void
    {
        unset($_SESSION['tokens']);
    }

    public static function deleteToken(string $token): void
    {
        unset($_SESSION['tokens'][$token]);
    }


  
}
