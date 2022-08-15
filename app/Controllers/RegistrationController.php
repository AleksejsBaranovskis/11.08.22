<?php

namespace App\Controllers;

use App\Auth;
use App\Redirect;
use App\Services\RegistrationService;
use App\Services\RegistrationServiceRequest;
use App\View\View;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Rules;

class RegistrationController
{
    private RegistrationService $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    public function showForm(): View
    {
        return new View('register.twig');
    }

    public function storeUser(): Redirect
    {
        $validator = new Rules\KeySet(
            (new Rules\Key('username', new Rules\AllOf(
                new Rules\Alnum(),
                new Rules\NoWhitespace(),
                new Rules\Length(3, 15)
            ))),
            (new Rules\Key('email', new Rules\AllOf(
                new Rules\Email()
            ))),
            (new Rules\Key('password', new Rules\AllOf(
                new Rules\NoWhitespace(),
                new Rules\Length(6)
            ))),
            (new Rules\Key('password_confirmation', new Rules\AllOf(
                new Rules\Equals($_POST['password'])
            )))
        );

        try {
            $validator->assert($_POST);

            $user = $this->registrationService->execute(
                new RegistrationServiceRequest(
                    $_POST['username'],
                    $_POST['email'],
                    $_POST['password']
                )
            );

            return new Redirect('/login');
        } catch (NestedValidationException $exception) {
            $_SESSION['errors'] = $exception->getMessages();

            return new Redirect('/registration');
        }
    }
}