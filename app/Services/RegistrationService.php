<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UsersRepository;

class RegistrationService
{
    private UsersRepository $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function execute(RegistrationServiceRequest $request): User
    {
        $user = new User(
            $request->getUsername(),
            $request->getEmail(),
            $request->getPassword()
        );

        $this->usersRepository->save($user);

        return $user;
    }
}