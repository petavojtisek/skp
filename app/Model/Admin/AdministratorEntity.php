<?php

namespace App\Model\Admin;

use App\Model\Base\IEntity;
use App\Model\Base\SecurityEntity;

class AdministratorEntity extends SecurityEntity implements IEntity
{
    public $admin_id;
    public $admin_group_id;
    public $user_name;
    public $user_password;
    public $user_pass_salt;
    public $name;
    public $surname;
    public $email;
    public $phone;
    public $last_logged_dt;
    public $disabled_dt;
    public $status;
    public $admin_lang;
    public $admin_group_name;

    public function getId() { return $this->admin_id; }
    public function setId($id): void { $this->setVariable('admin_id', $id, self::VALUE_TYPE_INTEGER); }

    public function getUserPassword() { return $this->user_password; }
}
