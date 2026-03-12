<?php

namespace App\Model\Login;

class LoginFacade
{
    /** @var LoginService */
    private $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function setStorage(string $storage): void
    {
        $this->loginService->setStorage($storage);
    }

    public function setUser($user): void
    {
        $this->loginService->setUser($user);
    }

    public function login(string $username, string $password): void
    {
        $this->loginService->login($username, $password);
    }
}
