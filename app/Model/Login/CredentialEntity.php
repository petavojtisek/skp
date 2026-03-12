<?php

namespace App\Model\Login;

use App\Model\Base\IEntity;
use App\Model\Base\SecurityEntity;

class CredentialEntity extends SecurityEntity implements IEntity
{
    public ?string $userName = null;
    public ?string $password = null;
    public mixed $userId = null;

    public function setUserName(mixed $userName): void { $this->setVariable('userName', $userName); }
    public function getUserName(): ?string { return $this->userName; }

    public function setPassword(mixed $password): void { $this->setVariable('password', $password); }
    public function getPassword(): ?string { return $this->password; }

    public function setUserId(mixed $userId): void { $this->setVariable('userId', $userId); }
    public function getUserId(): mixed { return $this->userId; }

    public function getId(): mixed { return $this->userId; }
    public function setId(mixed $id): void { $this->setUserId($id); }
}
