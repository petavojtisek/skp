<?php

namespace App\Model\Base;

use Nette\Security\Passwords;

abstract class SecurityEntity extends BaseEntity
{
    public function setPassword(string $password): void
    {
        $passwordHash = (new Passwords())->hash($password);
        $this->setVariable('userPassword', $passwordHash);
    }

    public function verifyPassword(string $password)
    {
        return (new Passwords())->verify($password, $this->userPassword);
    }
}
