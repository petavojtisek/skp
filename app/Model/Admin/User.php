<?php

namespace App\Model\Admin;

use Nette\Security\SimpleIdentity;

/**
 * Custom User class to allow identity data updates during session.
 */
class User extends \Nette\Security\User
{
    /**
     * Updates the data stored in the current identity without re-authenticating.
     */
    public function updateIdentityData(array $data): void
    {
        $identity = $this->getIdentity();
        if ($identity instanceof SimpleIdentity) {
            // Create a new identity with same ID and roles, but merged/new data
            $newIdentity = new SimpleIdentity($identity->getId(), $identity->getRoles(), $data);
            $this->getStorage()->saveAuthentication($newIdentity);
        }
    }
}
