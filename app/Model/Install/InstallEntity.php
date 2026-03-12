<?php

namespace App\Model\Install;

use App\Model\Base\BaseEntity;

class InstallEntity extends BaseEntity
{
    public mixed $install_id = null;
    public ?string $module_name = null;
    public mixed $installed = null;
    public ?string $path = null;

    public function getId(): mixed { return $this->install_id; }
    public function setId(mixed $id): void { $this->setVariable('install_id', $id, self::VALUE_TYPE_INTEGER); }
}
