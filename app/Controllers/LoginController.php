<?php

namespace App\Controllers;

use App\Auth;
use App\Redirect;
use App\Services\LoginService;
use App\Services\LoginServiceRequest;
use App\View\View;

class LoginController
{
    private LoginService $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function showForm(): View
    {
        if (Auth::isAuthorized()) {
            header('Location: /auth');

        }
        return new View('login.twig');
    }

    public function auth(): View
    {
        return new View ('auth.twig');
    }

    public function authUser(): Redirect
    {
        $this->loginService->execute(
            new LoginServiceRequest(
                $_POST['username'],
                $_POST['password']
            )
        );

        return new Redirect('/login');
    }

    public function logout(): Redirect
    {
        Auth::logout();

        return new Redirect('/login');
    }

}