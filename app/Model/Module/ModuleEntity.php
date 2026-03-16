<?php

namespace App\Model\Module;

use App\Model\Base\BaseEntity;

class ModuleEntity extends BaseEntity
{
    public mixed $module_id = null;
    public ?int $install_id = null;
    public ?int $module_type = null;
    public ?string $module_active = 'N';
    public ?string $module_name = null;
    public ?string $module_code_name = null;
    public ?string $module_class_name = null;

    public function getId(): mixed
    {
        return $this->module_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('module_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getInstallId(): ?int
    {
        return $this->install_id;
    }

    public function setInstallId(?int $install_id): void
    {
        $this->setVariable('install_id', $install_id, self::VALUE_TYPE_INTEGER);
    }

    public function getModuleType(): ?int
    {
        return $this->module_type;
    }

    public function setModuleType(?int $module_type): void
    {
        $this->setVariable('module_type', $module_type, self::VALUE_TYPE_INTEGER);
    }

    public function getModuleActive(): ?string
    {
        return $this->module_active;
    }

    public function setModuleActive(?string $module_active): void
    {
        $this->setVariable('module_active', $module_active, self::VALUE_TYPE_STRING);
    }

    public function getModuleName(): ?string
    {
        return $this->module_name;
    }

    public function setModuleName(?string $module_name): void
    {
        $this->setVariable('module_name', $module_name, self::VALUE_TYPE_STRING);
    }

    public function getModuleCodeName(): ?string
    {
        return $this->module_code_name;
    }

    public function setModuleCodeName(?string $module_code_name): void
    {
        $this->setVariable('module_code_name', $module_code_name, self::VALUE_TYPE_STRING);
    }

    public function getModuleClassName(): ?string
    {
        return $this->module_class_name;
    }

    public function setModuleClassName(?string $module_class_name): void
    {
        $this->setVariable('module_class_name', $module_class_name, self::VALUE_TYPE_STRING);
    }
}
