<?php

namespace App\Model\Login;

use App\Model\Base\BaseService;
use Nette\Security\User;
use App\Model\Log\LogFacade;

class LoginService extends BaseService
{
    private $storage;

    /** @var User */
    public $user;

    /** @var LogFacade */
    private $logFacade;

    public function __construct(User $user, LogFacade $logFacade)
    {
        $this->user = $user;
        $this->logFacade = $logFacade;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function setStorage($storage): void
    {
        $this->storage = $storage;
    }

    public function login($usr, $pass): void
    {
        if ($this->storage === 'admin') {
            $credentials = new CredentialEntity();
            $credentials->setUserName($usr);
            $credentials->setPassword($pass);
            
            $identity = $this->user->getAuthenticator()->authenticate(['admin', $credentials]);
            $this->user->login($identity);

            // LOG LOGIN
            $this->logFacade->logAction('System', 'LOGIN', 'Přihlášení uživatele: ' . $usr, (int)$this->user->getId());
        }
    }
}
