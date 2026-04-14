<?php

namespace App\Model\Admin;

use App\Model\Base\IEntity;
use App\Model\Base\SecurityEntity;

class AdministratorEntity extends SecurityEntity implements IEntity
{
    public mixed $admin_id = null;
    public mixed $admin_group_id = null;
    public ?string $admin_group_name = null;
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

    public function getId(): mixed { return $this->admin_id; }
    public function setId(mixed $id): void { $this->setVariable('admin_id', $id, self::VALUE_TYPE_INTEGER); }

    public function getAdminGroupId(): mixed { return $this->admin_group_id; }
    public function setAdminGroupId(mixed $id): void { $this->setVariable('admin_group_id', $id, self::VALUE_TYPE_INTEGER); }

    public function getUserName(): ?string { return $this->user_name; }
    public function setUserName(?string $user_name): void { $this->setVariable('user_name', $user_name, self::VALUE_TYPE_STRING); }

    public function getUserPassword(): ?string { return $this->user_password; }
    // setPassword is inherited from SecurityEntity

    public function getUserPassSalt(): ?string { return $this->user_pass_salt; }
    public function setUserPassSalt(?string $user_pass_salt): void { $this->setVariable('user_pass_salt', $user_pass_salt, self::VALUE_TYPE_STRING); }

    public function getName(): ?string { return $this->name; }
    public function setName(?string $name): void { $this->setVariable('name', $name, self::VALUE_TYPE_STRING); }

    public function getSurname(): ?string { return $this->surname; }
    public function setSurname(?string $surname): void { $this->setVariable('surname', $surname, self::VALUE_TYPE_STRING); }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): void { $this->setVariable('email', $email, self::VALUE_TYPE_STRING); }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): void { $this->setVariable('phone', $phone, self::VALUE_TYPE_STRING); }

    public function getLastLoggedDt($format = null) { return $this->getDateTime($this->last_logged_dt, $format); }
    public function setLastLoggedDt(mixed $dt): void { $this->setVariable('last_logged_dt', $dt, self::VALUE_TYPE_DATE); }

    public function getDisabledDt($format = null) { return $this->getDateTime($this->disabled_dt, $format); }
    public function setDisabledDt(mixed $dt): void { $this->setVariable('disabled_dt', $dt, self::VALUE_TYPE_DATE); }

    public function getStatus(): ?int { return $this->status; }
    public function setStatus(?int $status): void { $this->setVariable('status', $status, self::VALUE_TYPE_INTEGER); }

    public function getAdminLang(): ?int { return $this->admin_lang; }
    public function setAdminLang(?int $admin_lang): void { $this->setVariable('admin_lang', $admin_lang, self::VALUE_TYPE_INTEGER); }

    public function getAdminGroupName() : ?string
    {
        return $this->admin_group_name;
    }

    public function setAdminGroupName(?string $adminGroupName) : void
    {
        $this->admin_group_name = $adminGroupName;
    }


}
