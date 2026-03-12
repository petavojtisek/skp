<?php

namespace App\Model\Admin;

use Nette\InvalidArgumentException;
use Nette\Security\IAuthenticator;
use Nette\Security\IIdentity;

class SystemAuthenticator implements IAuthenticator
{
    private array $authenticators = [];

    public function addAuthenticator($key, $authenticator): static
    {
        if ($authenticator instanceof IAuthenticator) {
            $this->authenticators[$key] = $authenticator->authenticate(...);
        } elseif (is_callable($authenticator)) {
            $this->authenticators[$key] = $authenticator;
        } else {
            throw new InvalidArgumentException('Authenticator must be callable or instance of IAuthenticator.');
        }
        return $this;
    }

    public function authenticate(array $args): mixed
    {
        $key = array_shift($args);
        if (!isset($this->authenticators[$key])) {
            throw new InvalidArgumentException("Authenticator named '$key' is not registered.");
        }
        return call_user_func($this->authenticators[$key], $args);
    }
}
