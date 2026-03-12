<?php

namespace App\Model\Admin;

use App\Model\Login\CredentialEntity;
use Dibi\Connection;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\SimpleIdentity;
use Nette\Security\Passwords;

class AdminAuthenticator implements IAuthenticator
{
    /** @var Connection */
    public $dibi;

    public function __construct(Connection $connection)
    {
        $this->dibi = $connection;
    }

    public function authenticate(array $credentials): SimpleIdentity
    {
        [$credential] = $credentials;

        if ($credential->getUserName() !== null) {
            $adminData = $this->dibi->select('*')
                ->from('admin')
                ->where('user_name = %s', $credential->getUserName())
                ->fetch();

            if ($adminData) {
                $administratorEntity = new AdministratorEntity((array)$adminData, false);

                if (!empty($administratorEntity->disabled_dt)) {
                    throw new AuthenticationException('User is banned', self::IDENTITY_NOT_FOUND);
                }

                if (!(new Passwords())->verify($credential->getPassword(), $adminData->user_password)) {
                    throw new AuthenticationException('Wrong password', self::IDENTITY_NOT_FOUND);
                }

                return new SimpleIdentity($administratorEntity->getId(), $administratorEntity->role, (array)$adminData);
            }
        }
        
        throw new AuthenticationException('User not found', self::IDENTITY_NOT_FOUND);
    }
}
