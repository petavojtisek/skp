<?php

namespace App\Model\Login;

use App\Model\Base\IEntity;
use App\Model\Base\SecurityEntity;

class CredentialEntity extends SecurityEntity implements IEntity
{
    public ?string $user_name = null;
    public ?string $password = null;
    public mixed $user_id = null;

    public function getUserName(): ?string { return $this->user_name; }
    public function setUserName(?string $user_name): void { $this->setVariable('user_name', $user_name, self::VALUE_TYPE_STRING); }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(?string $password): void { $this->setVariable('password', $password, self::VALUE_TYPE_STRING); }

    public function getUserId(): mixed { return $this->user_id; }
    public function setUserId(mixed $user_id): void { $this->setVariable('user_id', $user_id, self::VALUE_TYPE_INTEGER); }

    public function getId(): mixed { return $this->user_id; }
    public function setId(mixed $id): void { $this->setUserId($id); }
}
