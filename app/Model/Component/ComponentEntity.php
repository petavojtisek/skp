<?php

namespace App\Model\Component;

use App\Model\Base\BaseEntity;

class ComponentEntity extends BaseEntity
{
    public mixed $component_id = null;
    public ?string $component_name = null;
    public ?int $module_id = null;
    public ?string $inserted = null;
    public ?string $code_name = null;

    // From JOIN
    public ?string $module_class_name = null;
    public ?string $module_code_name = null;

    public function getId(): mixed
    {
        return $this->component_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('component_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getComponentName(): ?string
    {
        return $this->component_name;
    }

    public function setComponentName(?string $name): void
    {
        $this->setVariable('component_name', $name, self::VALUE_TYPE_STRING);
    }

    public function getModuleId(): ?int
    {
        return $this->module_id;
    }

    public function setModuleId(?int $id): void
    {
        $this->setVariable('module_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getCodeName(): ?string
    {
        return $this->code_name;
    }

    public function setCodeName(?string $code): void
    {
        $this->setVariable('code_name', $code, self::VALUE_TYPE_STRING);
    }

    public function getModuleClassName(): ?string
    {
        return $this->module_class_name;
    }

    public function getModuleCodeName(): ?string
    {
        return $this->module_code_name;
    }
}
