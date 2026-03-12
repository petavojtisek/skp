<?php

namespace App\Model\Login;

use App\Model\Base\IEntity;
use App\Model\Base\SecurityEntity;

class CredentialEntity extends SecurityEntity implements IEntity
{
    public $userName;
    public $password;
    public $userId;

    public function setUserName($userName): void { $this->setVariable('userName', $userName); }
    public function getUserName() { return $this->userName; }

    public function setPassword($password): void { $this->setVariable('password', $password); }
    public function getPassword() { return $this->password; }

    public function setUserId($userId): void { $this->setVariable('userId', $userId); }
    public function getUserId() { return $this->userId; }
}
