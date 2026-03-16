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

    public function getModuleName(): ?string
    {
        return $this->module_name;
    }

    public function setModuleName(?string $module_name): void
    {
        $this->setVariable('module_name', $module_name, self::VALUE_TYPE_STRING);
    }

    public function getInstalled(): mixed
    {
        return $this->installed;
    }

    public function setInstalled(mixed $installed): void
    {
        $this->setVariable('installed', $installed, self::VALUE_TYPE_INTEGER);
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->setVariable('path', $path, self::VALUE_TYPE_STRING);
    }
}
