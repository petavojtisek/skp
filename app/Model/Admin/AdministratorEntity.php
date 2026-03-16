<?php

namespace App\Model\Admin;

use App\Model\Base\IEntity;
use App\Model\Base\SecurityEntity;

class AdministratorEntity extends SecurityEntity implements IEntity
{
    public mixed $admin_id = null;
    public mixed $admin_group_id = null;
    public ?string $user_name = null;
    public ?string $user_password = null;
    public ?string $user_pass_salt = null;
    public ?string $name = null;
    public ?string $surname = null;
    public ?string $email = null;
    public ?string $phone = null;
    public mixed $last_logged_dt = null;
    public mixed $disabled_dt = null;
    public mixed $status = null;
    public mixed $admin_lang = null;
    public ?string $admin_group_name = null;

    public function getId(): mixed { return $this->admin_id; }
    public function setId(mixed $id): void { $this->setVariable('admin_id', $id, self::VALUE_TYPE_INTEGER); }

    public function getGroupId(): mixed { return $this->admin_group_id; }
    public function setGroupId(mixed $id): void { $this->setVariable('admin_group_id', $id, self::VALUE_TYPE_INTEGER); }

    public function getUserPassword(): ?string { return $this->user_password; }
}
