<?php

namespace App\Services;

use App\Repositories\Exceptions\RecordNotFoundException;
use App\Repositories\MySQLUsersRepository;

class LoginService
{
    private MySQLUsersRepository $usersRepository;

    public function __construct(MySQLUsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function execute(LoginServiceRequest $request): void
    {
        try {
            $user = $this->usersRepository->getByUsername($request->getUsername());
            if (!password_verify(($request->getPassword()), $user->getPassword())) {
                echo "Password is incorrect";
                return;
            }

            $_SESSION['auth_id'] = $user->getId();

        } catch (RecordNotFoundException $e) {
            echo "No such user";
        }
    }
}