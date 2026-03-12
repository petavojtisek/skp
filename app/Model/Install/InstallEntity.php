<?php

namespace App\Model\Install;

use App\Model\Base\BaseEntity;

class InstallEntity extends BaseEntity
{
    public $install_id;
    public $module_name;
    public $installed;
    public $path;

    public function getId() { return $this->install_id; }
    public function setId($id): void { $this->setVariable('install_id', $id, self::VALUE_TYPE_INTEGER); }
}
