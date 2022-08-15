<?php

namespace App;

class Auth
{

    public static function isAuthorized(): bool
    {
        return isset($_SESSION['auth_id']);
    }

    public static function authorize(int $id): void
    {
        $_SESSION['auth_id'] = $id;
    }

    public static function logout(): void
    {
        unset($_SESSION['auth_id']);
    }
}