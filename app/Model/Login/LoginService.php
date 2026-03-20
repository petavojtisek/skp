<?php

namespace App\Model\Login;

use App\Model\Base\BaseService;
use Nette\Security\User;
use App\Model\Log\LogFacade;

class LoginService extends BaseService
{
    private $storage;


    public function __construct()
    {

    }


    public function setStorage($storage): void
    {
        $this->storage = $storage;
    }

    public function getStorage(): ?string
    {
        return $this->storage;
    }

    public function getCredential($usr, $pass): ?CredentialEntity
    {
        if ($this->storage === 'admin') {
            $credentials = new CredentialEntity();
            $credentials->setUserName($usr);
            $credentials->setPassword($pass);
            return  $credentials;
        }

        return null;
    }
}
